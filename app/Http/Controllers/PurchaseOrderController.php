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
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller
{

    public function index()
    {
        $orders = PurchaseOrder::with('supplier')->latest()->paginate(10);
        $suppliers = Supplier::all();
        $gasTypes = GasType::all();

        $rates = DB::table('gas_type_supplier')
            ->select('supplier_id','gas_type_id','rate')
            ->get();

        return view('purchase-orders.index', compact('orders', 'suppliers', 'gasTypes', 'rates'));
    }


    private function generatePONumber()
    {
        return 'PO-' . str_pad(PurchaseOrder::count() + 1, 5, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'order_date'  => 'required|date',
            'items'       => 'required|array'
        ]);

        $po = PurchaseOrder::create([
            'po_number'   => $this->generatePONumber(),
            'supplier_id' => $request->supplier_id,
            'order_date'  => $request->order_date,
            'status'      => 'Pending'
        ]);

        $total = 0;

        foreach ($request->items as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];

            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'gas_type_id'       => $item['gas_type_id'],
                'quantity'          => $item['quantity'],
                'unit_price'        => $item['unit_price'],
                'total'             => $lineTotal
            ]);

            $total += $lineTotal;
        }

        $po->update(['total_amount' => $total]);

        return back()->with('success', 'Purchase Order created successfully.');
    }

    public function updateStatus(PurchaseOrder $po, $status)
    {
        $po->update(['status' => $status]);
        return back()->with('success', 'PO status updated.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();
        return back()->with('success', 'PO deleted.');
    }
}
