<?php

namespace App\Http\Controllers;

use App\Models\{
    Grn,
    GrnItem,
    Supplier,
    PurchaseOrder,
    PurchaseOrderItem,
    Stock
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrnController extends Controller
{

    public function index()
    {
        $grns = Grn::with(['supplier','purchaseOrder'])->latest()->paginate(12);
        $suppliers = Supplier::all();
        $purchaseOrders = PurchaseOrder::where('status', '!=', 'Completed')->get();

        return view('grns.index', compact('grns','suppliers','purchaseOrders'));
    }

    public function getPoItems(PurchaseOrder $po)
    {
        $po->load('items.gasType');

        $items = $po->items->map(function (PurchaseOrderItem $item) {
            return [
                'gas_type_id'   => $item->gas_type_id,
                'gas_type_name' => $item->gasType->name,
                'ordered_qty'   => (int) $item->quantity,
                'received_qty'  => (int) $item->quantity,
                'rejected_qty'  => 0,
            ];
        });

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'received_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.gas_type_id' => 'required|exists:gas_types,id',
            'items.*.ordered_qty' => 'required|integer|min:0',
            'items.*.received_qty' => 'required|integer|min:0',
            'items.*.rejected_qty' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $grnNumber = 'GRN-' . str_pad(Grn::count() + 1, 5, '0', STR_PAD_LEFT);

            $grn = Grn::create([
                'grn_number' => $grnNumber,
                'supplier_id' => $request->supplier_id,
                'purchase_order_id' => $request->purchase_order_id,
                'received_date' => $request->received_date,
                'status' => 'Pending',
                'approved' => false
            ]);

            foreach ($request->items as $it) {
                GrnItem::create([
                    'grn_id' => $grn->id,
                    'gas_type_id' => $it['gas_type_id'],
                    'ordered_qty' => $it['ordered_qty'],
                    'received_qty' => $it['received_qty'],
                    'damaged_qty' => $it['damaged_qty'] ?? 0,
                    'rejected_qty' => $it['rejected_qty'] ?? 0,
                ]);
            }

            DB::commit();

            return back()->with('success', 'GRN saved and awaiting approval.');

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Failed to save GRN: ' . $e->getMessage());
        }
    }

    public function approve(Grn $grn)
    {
        if ($grn->approved) {
            return back()->with('info', 'GRN has already been approved.');
        }

        DB::beginTransaction();
        try {
            $grn->update([
                'approved' => true,
                'approved_at' => now(),
                'status' => 'Approved'
            ]);

            $grn->load('items');

            foreach ($grn->items as $item) {
                $netQty = max(0, ($item->received_qty ?? 0) - ($item->rejected_qty ?? 0));

                if ($netQty <= 0) {
                    continue;
                }

                $stock = Stock::firstOrCreate(
                    ['gas_type_id' => $item->gas_type_id],
                    ['full_qty' => 0, 'empty_qty' => 0]
                );

                $stock->increment('full_qty', $netQty);
            }

            $po = $grn->purchaseOrder()->first();
            if ($po) {
                $poItems = $po->items()->get()->mapWithKeys(function ($pi) {
                    return [$pi->gas_type_id => (int)$pi->quantity];
                })->toArray();

                $receivedSums = DB::table('grn_items')
                    ->join('grns', 'grn_items.grn_id', '=', 'grns.id')
                    ->where('grns.purchase_order_id', $po->id)
                    ->where('grns.approved', true)
                    ->select('grn_items.gas_type_id', DB::raw('SUM(grn_items.received_qty - COALESCE(grn_items.rejected_qty,0)) as received'))
                    ->groupBy('grn_items.gas_type_id')
                    ->pluck('received', 'gas_type_id')
                    ->toArray();

                $allReceived = true;
                foreach ($poItems as $gasTypeId => $orderedQty) {
                    $received = isset($receivedSums[$gasTypeId]) ? (int)$receivedSums[$gasTypeId] : 0;
                    if ($received < $orderedQty) {
                        $allReceived = false;
                        break;
                    }
                }

                $po->status = $allReceived ? 'Completed' : 'Partial';
                $po->save();
            }

            DB::commit();
            return back()->with('success', 'GRN approved and stock updated. PO status: ' . ($po->status ?? 'N/A'));

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Failed to approve GRN: ' . $e->getMessage());
        }
    }

    public function destroy(Grn $grn)
    {
        if ($grn->approved) {
            return back()->with('error', 'Cannot delete an approved GRN.');
        }

        $grn->delete();
        return back()->with('success', 'GRN deleted.');
    }
}
