<?php

namespace App\Http\Controllers;

use App\Models\{
    PurchaseOrder,
    PurchaseOrderItem,
    Supplier,
    GasType
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $orders = PurchaseOrder::with('supplier', 'items.gasType')
            ->latest()
            ->paginate(10);
        
        $suppliers = Supplier::all();
        $gasTypes = GasType::all();

        $rates = DB::table('gas_type_supplier')
            ->select('supplier_id', 'gas_type_id', 'rate')
            ->get();

        return view('purchase-orders.index', compact('orders', 'suppliers', 'gasTypes', 'rates'));
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier', 'items.gasType', 'grns');
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Generate unique PO number
     */
    private function generatePONumber()
    {
        $latestPO = PurchaseOrder::latest('id')->first();
        $nextNum = ($latestPO ? intval(substr($latestPO->po_number, 3)) : 0) + 1;
        return 'PO-' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date|after_or_equal:today',
            'delivery_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.gas_type_id' => 'required|exists:gas_types,id',
            'items.*.quantity' => 'required|integer|min:1|max:10000',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::create([
                'po_number' => $this->generatePONumber(),
                'supplier_id' => $request->supplier_id,
                'order_date' => $request->order_date,
                'delivery_date' => $request->delivery_date,
                'notes' => $request->notes,
                'status' => 'Pending'
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'gas_type_id' => $item['gas_type_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $lineTotal
                ]);

                $total += $lineTotal;
            }

            $po->update(['total_amount' => $total]);
            DB::commit();

            return back()->with('success', 'Purchase Order created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Failed to create PO: ' . $e->getMessage());
        }
    }

    public function updateStatus(PurchaseOrder $po, $status)
    {
        $validStatuses = ['Pending', 'Approved', 'Completed'];
        
        if (!in_array($status, $validStatuses)) {
            return back()->with('error', 'Invalid status.');
        }

        // Only allow Completed status if fully received
        if ($status === 'Completed' && !$po->isFullyReceived()) {
            return back()->with('error', 'Cannot complete PO until all items are received.');
        }

        $po->update(['status' => $status]);
        
        return back()->with('success', 'PO status updated to ' . $status);
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->grns()->exists()) {
            return back()->with('error', 'Cannot delete PO with existing GRNs.');
        }

        $purchaseOrder->delete();
        return back()->with('success', 'PO deleted.');
    }
}
