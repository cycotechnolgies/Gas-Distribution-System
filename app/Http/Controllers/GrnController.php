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
        $grns = Grn::with(['supplier','purchaseOrder', 'items.gasType'])
            ->latest()
            ->paginate(12);
        
        $suppliers = Supplier::all();
        $purchaseOrders = PurchaseOrder::where('status', '!=', 'Completed')
            ->with('items.gasType')
            ->get();

        return view('grns.index', compact('grns','suppliers','purchaseOrders'));
    }

    public function show(Grn $grn)
    {
        $grn->load('supplier', 'purchaseOrder.items.gasType', 'items.gasType');
        return view('grns.show', compact('grn'));
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
            'items.*.damaged_qty' => 'nullable|integer|min:0',
            'items.*.rejected_qty' => 'nullable|integer|min:0',
            'items.*.rejection_notes' => 'nullable|string|max:500',
            'variance_notes' => 'nullable|string|max:500',
            'rejection_notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::findOrFail($request->purchase_order_id);

            // Validate supplier matches PO
            if ($po->supplier_id != $request->supplier_id) {
                return back()->with('error', 'Selected supplier does not match PO supplier.');
            }

            $grnNumber = 'GRN-' . str_pad(Grn::count() + 1, 5, '0', STR_PAD_LEFT);

            $grn = Grn::create([
                'grn_number' => $grnNumber,
                'supplier_id' => $request->supplier_id,
                'purchase_order_id' => $request->purchase_order_id,
                'received_date' => $request->received_date,
                'status' => 'Pending',
                'approved' => false,
                'variance_notes' => $request->variance_notes,
                'rejection_notes' => $request->rejection_notes
            ]);

            $hasVariance = false;

            foreach ($request->items as $it) {
                $item = GrnItem::create([
                    'grn_id' => $grn->id,
                    'gas_type_id' => $it['gas_type_id'],
                    'ordered_qty' => $it['ordered_qty'],
                    'received_qty' => $it['received_qty'],
                    'damaged_qty' => $it['damaged_qty'] ?? 0,
                    'rejected_qty' => $it['rejected_qty'] ?? 0,
                    'rejection_notes' => $it['rejection_notes'] ?? null,
                ]);

                if ($item->getVariance() != 0) {
                    $hasVariance = true;
                }
            }

            DB::commit();

            $message = 'GRN saved and awaiting approval.';
            if ($hasVariance) {
                $message .= ' ⚠️ Variance detected - please review.';
            }
            if ($grn->getTotalRejected() > 0) {
                $message .= ' ⚠️ Items rejected - please review.';
            }

            return back()->with('success', $message);

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

            // Update stock with net received quantities
            foreach ($grn->items as $item) {
                $netQty = $item->getNetReceived();

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

                // Get all approved GRNs for this PO and sum received quantities
                $receivedSums = DB::table('grn_items')
                    ->join('grns', 'grn_items.grn_id', '=', 'grns.id')
                    ->where('grns.purchase_order_id', $po->id)
                    ->where('grns.approved', true)
                    ->select('grn_items.gas_type_id', DB::raw('SUM(grn_items.received_qty - COALESCE(grn_items.rejected_qty,0)) as received'))
                    ->groupBy('grn_items.gas_type_id')
                    ->pluck('received', 'gas_type_id')
                    ->toArray();

                // Check if fully received
                $allReceived = true;
                $partialItems = [];
                
                foreach ($poItems as $gasTypeId => $orderedQty) {
                    $received = isset($receivedSums[$gasTypeId]) ? (int)$receivedSums[$gasTypeId] : 0;
                    if ($received < $orderedQty) {
                        $allReceived = false;
                        $partialItems[] = [
                            'gas_type_id' => $gasTypeId,
                            'ordered' => $orderedQty,
                            'received' => $received,
                            'short' => $orderedQty - $received
                        ];
                    }
                }

                // Update PO status
                if ($allReceived) {
                    $po->status = 'Completed';
                } else {
                    $po->status = 'Partial';
                }
                $po->save();

                // Build success message
                $message = 'GRN approved and stock updated. ';
                if ($allReceived) {
                    $message .= 'PO fully received and marked Completed.';
                } else {
                    $message .= 'PO marked as Partial - ';
                    $shortCount = count($partialItems);
                    $message .= $shortCount . ' item(s) still outstanding.';
                }

                DB::commit();
                return back()->with('success', $message);
            }

            DB::commit();
            return back()->with('success', 'GRN approved and stock updated.');

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

    public function getPoDetails(PurchaseOrder $po)
    {
        $po->load('items.gasType', 'supplier');

        // Get already received quantities from approved GRNs
        $receivedSums = DB::table('grn_items')
            ->join('grns', 'grn_items.grn_id', '=', 'grns.id')
            ->where('grns.purchase_order_id', $po->id)
            ->where('grns.approved', true)
            ->select('grn_items.gas_type_id', DB::raw('SUM(grn_items.received_qty - COALESCE(grn_items.rejected_qty,0)) as received'))
            ->groupBy('grn_items.gas_type_id')
            ->pluck('received', 'gas_type_id')
            ->toArray();

        $items = $po->items->map(function (PurchaseOrderItem $item) use ($receivedSums) {
            $alreadyReceived = isset($receivedSums[$item->gas_type_id]) ? (int)$receivedSums[$item->gas_type_id] : 0;
            $remaining = max(0, $item->quantity - $alreadyReceived);

            return [
                'gas_type_id'   => $item->gas_type_id,
                'gas_type_name' => $item->gasType->name,
                'ordered_qty'   => (int)$item->quantity,
                'received_qty'  => (int)$item->quantity,
                'damaged_qty'   => 0,
                'rejected_qty'  => 0,
                'already_received' => $alreadyReceived,
                'remaining' => $remaining
            ];
        });

        return response()->json([
            'id' => $po->id,
            'po_number' => $po->po_number,
            'po_date' => $po->order_date->format('Y-m-d'),
            'supplier_id' => $po->supplier_id,
            'supplier_name' => $po->supplier->name,
            'status' => $po->status,
            'items' => $items
        ]);
    }
}

