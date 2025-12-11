<?php

namespace App\Http\Controllers;

use App\Models\{Order, OrderItem, Customer, GasType, Stock, DeliveryRoute};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::with(['customer','deliveryRoute'])->latest()->paginate(12);
        $customers = Customer::all();
        $gasTypes = GasType::all();
        $routes = DeliveryRoute::all();

        // Preload stocks for convenience (optional)
        $stocks = DB::table('stocks')->select('gas_type_id','full_qty')->get()->pluck('full_qty','gas_type_id');

        return view('orders.index', compact('orders','customers','gasTypes','routes','stocks'));
    }

    private function generateOrderNumber()
    {
        return 'ORD-' . str_pad(Order::count() + 1, 6, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'delivery_route_id' => 'nullable|exists:delivery_routes,id',
            'urgent' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.gas_type_id' => 'required|exists:gas_types,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => $request->customer_id,
                'delivery_route_id' => $request->delivery_route_id,
                'order_date' => $request->order_date,
                'status' => 'Pending',
                'urgent' => (bool) $request->urgent,
            ]);

            $total = 0;
            foreach ($request->items as $it) {
                $line = (float)$it['quantity'] * (float)$it['unit_price'];
                $total += $line;

                OrderItem::create([
                    'order_id' => $order->id,
                    'gas_type_id' => $it['gas_type_id'],
                    'quantity' => $it['quantity'],
                    'unit_price' => $it['unit_price'],
                    'total' => $line
                ]);
            }

            $order->update(['total_amount' => $total]);

            DB::commit();
            return back()->with('success','Order created.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error','Failed to create order: ' . $e->getMessage());
        }
    }

    // change status: Loaded, Delivered, Completed
    public function updateStatus(Request $request, Order $order, $status)
    {
        $allowed = ['Pending','Loaded','Delivered','Completed'];
        if (!in_array($status, $allowed)) {
            return back()->with('error','Invalid status.');
        }

        DB::beginTransaction();
        try {
            // When marking Delivered, adjust stock
            if ($status === 'Delivered' && $order->status !== 'Delivered') {

                // For each order item ensure stock is available and decrement
                $order->load('items');

                foreach ($order->items as $item) {

                    // If it was already adjusted earlier (delivered_adjusted), skip
                    if ($item->delivered_adjusted) continue;

                    $stock = Stock::where('gas_type_id', $item->gas_type_id)->first();

                    $needed = (int)$item->quantity;

                    if (!$stock || $stock->full_qty < $needed) {
                        DB::rollBack();
                        return back()->with('error', "Insufficient stock for {$item->gasType->name}. Needed: {$needed}, Available: " . ($stock->full_qty ?? 0));
                    }

                    // decrement full, increment empty
                    $stock->decrement('full_qty', $needed);
                    $stock->increment('empty_qty', $needed);

                    // mark item as adjusted to avoid double adjustment
                    $item->update(['delivered_adjusted' => true]);
                }
            }

            // simple state transition
            $order->status = $status;
            $order->save();

            DB::commit();
            return back()->with('success','Order status updated to '.$status);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error','Failed to update status: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order->load(['items.gasType','customer','deliveryRoute']);
        return view('orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        // Disallow deleting delivered/completed orders
        if (in_array($order->status, ['Delivered','Completed'])) {
            return back()->with('error','Cannot delete delivered or completed orders.');
        }
        $order->delete();
        return back()->with('success','Order deleted.');
    }
}
