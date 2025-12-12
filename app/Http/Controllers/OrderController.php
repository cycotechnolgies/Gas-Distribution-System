<?php

namespace App\Http\Controllers;

use App\Models\{Order, OrderItem, Customer, GasType, Stock, DeliveryRoute};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display list of orders
     */
    public function index()
    {
        $orders = Order::with(['customer', 'deliveryRoute', 'items.gasType'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total_orders' => Order::count(),
            'pending' => Order::where('status', 'Pending')->count(),
            'loaded' => Order::where('status', 'Loaded')->count(),
            'delivered' => Order::where('status', 'Delivered')->count(),
            'completed' => Order::where('status', 'Completed')->count(),
            'urgent' => Order::where('is_urgent', true)->count()
        ];

        return view('orders.index', compact('orders', 'stats'));
    }

    /**
     * Show form for creating new order
     */
    public function create()
    {
        $customers = Customer::where('status', 'Active')->get();
        $gasTypes = GasType::all();
        $routes = DeliveryRoute::where('route_status', 'Active')->get();

        return view('orders.create', compact('customers', 'gasTypes', 'routes'));
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber()
    {
        $latestOrder = Order::latest('id')->first();
        $nextNum = ($latestOrder ? intval(substr($latestOrder->order_number, 4)) : 0) + 1;
        return 'ORD-' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Store newly created order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'delivery_route_id' => 'nullable|exists:delivery_routes,id',
            'is_urgent' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.gas_type_id' => 'required|exists:gas_types,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Check customer credit
            $customer = Customer::find($validated['customer_id']);
            
            // Calculate order total
            $orderTotal = 0;
            foreach ($validated['items'] as $item) {
                $orderTotal += $item['quantity'] * $item['unit_price'];
            }

            // Check if customer has sufficient credit
            if ($customer->getCreditAvailable() < $orderTotal) {
                DB::rollBack();
                return back()->with('error', "Insufficient credit. Available: LKR " . number_format($customer->getCreditAvailable(), 2));
            }

            // Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => $validated['customer_id'],
                'delivery_route_id' => $validated['delivery_route_id'] ?? null,
                'order_date' => $validated['order_date'],
                'status' => 'Pending',
                'is_urgent' => (bool) ($validated['is_urgent'] ?? false),
                'order_total' => $orderTotal,
                'notes' => $validated['notes'] ?? null
            ]);

            // Create order items
            foreach ($validated['items'] as $itemData) {
                $lineTotal = $itemData['quantity'] * $itemData['unit_price'];
                OrderItem::create([
                    'order_id' => $order->id,
                    'gas_type_id' => $itemData['gas_type_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'line_total' => $lineTotal,
                    'notes' => $itemData['notes'] ?? null
                ]);
            }

            // Update customer cylinder tracking
            foreach ($validated['items'] as $itemData) {
                $customer->recordCylinderTransaction(
                    $itemData['gas_type_id'],
                    'Issued',
                    $itemData['quantity'],
                    'ORD-' . $order->id,
                    'Order #' . $order->order_number
                );
            }

            // Update customer balance
            $customer->updateBalance($orderTotal);

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Order created successfully: ' . $order->order_number);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Display order details
     */
    public function show(Order $order)
    {
        $order->load(['items.gasType', 'customer', 'deliveryRoute']);
        
        $stats = [
            'item_count' => $order->getItemCount(),
            'total_qty' => $order->getTotalQuantity(),
            'order_total' => $order->getOrderTotal(),
            'can_transition' => $order->canTransitionTo('Loaded') || 
                                $order->canTransitionTo('Delivered') || 
                                $order->canTransitionTo('Completed')
        ];

        return view('orders.show', compact('order', 'stats'));
    }

    /**
     * Update order status with validation
     */
    public function updateStatus(Request $request, Order $order, $status)
    {
        // Validate status
        $validStatuses = ['Pending', 'Loaded', 'Delivered', 'Completed', 'Cancelled'];
        if (!in_array($status, $validStatuses)) {
            return back()->with('error', 'Invalid status.');
        }

        DB::beginTransaction();
        try {
            // Check if transition is allowed
            if (!$order->canTransitionTo($status)) {
                DB::rollBack();
                return back()->with('error', "Cannot transition from {$order->status} to {$status}.");
            }

            // When marking Delivered, adjust stock
            if ($status === 'Delivered' && $order->status !== 'Delivered') {
                $order->load('items');

                foreach ($order->items as $item) {
                    if ($item->delivered_adjusted) continue;

                    $stock = Stock::where('gas_type_id', $item->gas_type_id)->first();
                    $needed = (int)$item->quantity;

                    if (!$stock || $stock->full_qty < $needed) {
                        DB::rollBack();
                        return back()->with('error', "Insufficient stock for {$item->gasType->name}. Needed: {$needed}, Available: " . ($stock->full_qty ?? 0));
                    }

                    $stock->decrement('full_qty', $needed);
                    $stock->increment('empty_qty', $needed);
                    $item->update(['delivered_adjusted' => true]);
                }
            }

            // Update order status and timestamp
            $order->updateOrderStatus($status);

            DB::commit();
            return back()->with('success', 'Order status updated to ' . $status);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Assign order to delivery route
     */
    public function assignRoute(Request $request, Order $order)
    {
        $validated = $request->validate([
            'delivery_route_id' => 'required|exists:delivery_routes,id'
        ]);

        if ($order->assignToRoute($validated['delivery_route_id'])) {
            return back()->with('success', 'Order assigned to route successfully.');
        }

        return back()->with('error', 'Failed to assign route.');
    }

    /**
     * Mark order as urgent
     */
    public function markUrgent(Order $order)
    {
        if ($order->markUrgent()) {
            return back()->with('success', 'Order marked as urgent.');
        }
        return back()->with('error', 'Failed to mark order as urgent.');
    }

    /**
     * Unmark order as urgent
     */
    public function unmarkUrgent(Order $order)
    {
        if ($order->unmarkUrgent()) {
            return back()->with('success', 'Order unmarked as urgent.');
        }
        return back()->with('error', 'Failed to unmark order as urgent.');
    }

    /**
     * Delete order
     */
    public function destroy(Order $order)
    {
        if (!$order->canBeDeleted()) {
            return back()->with('error', 'Cannot delete orders with status: ' . $order->status);
        }

        // Reverse cylinder transaction
        $order->load('items', 'customer');
        foreach ($order->items as $item) {
            $order->customer->recordCylinderTransaction(
                $item->gas_type_id,
                'Returned',
                $item->quantity,
                'ORD-CANCEL-' . $order->id,
                'Order cancelled'
            );
        }

        // Reverse balance
        $order->customer->updateBalance(-$order->order_total);

        // Delete order
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }

    /**
     * Get customer pricing for a gas type (API endpoint)
     */
    public function getCustomerPrice(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'gas_type_id' => 'required|exists:gas_types,id'
        ]);

        $customer = Customer::find($request->customer_id);
        $price = $customer->getPriceForGasType($request->gas_type_id);

        return response()->json([
            'price' => $price,
            'source' => $price ? 'custom' : 'category'
        ]);
    }

    /**
     * Get customer credit information
     */
    public function getCustomerCredit(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id'
        ]);

        $customer = Customer::find($request->customer_id);

        return response()->json([
            'credit_limit' => $customer->credit_limit,
            'outstanding_balance' => $customer->getOutstandingBalance(),
            'available_credit' => $customer->getCreditAvailable(),
            'is_over_credit' => $customer->isOverCredit()
        ]);
    }
}
