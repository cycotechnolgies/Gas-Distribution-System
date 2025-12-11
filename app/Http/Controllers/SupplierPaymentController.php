<?php

namespace App\Http\Controllers;

use App\Models\{
    SupplierPayment,
    Supplier,
    PurchaseOrder
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierPaymentController extends Controller
{
    public function index()
    {
        $payments = SupplierPayment::with('supplier', 'purchaseOrder')
            ->latest()
            ->paginate(15);

        $suppliers = Supplier::all();
        $purchaseOrders = PurchaseOrder::where('status', '!=', 'Pending')->get();

        return view('supplier-payments.index', compact('payments', 'suppliers', 'purchaseOrders'));
    }

    private function generatePaymentRef()
    {
        return 'PAY-' . date('Ymd') . '-' . str_pad(SupplierPayment::count() + 1, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|in:Cheque,Bank Transfer,Cash,Online',
            'cheque_number' => 'nullable|string|max:50',
            'cheque_date' => 'nullable|date|required_if:payment_mode,Cheque',
            'payment_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::findOrFail($request->purchase_order_id);

            // Validate supplier matches PO
            if ($po->supplier_id != $request->supplier_id) {
                return back()->with('error', 'Selected supplier does not match PO supplier.');
            }

            // Create payment record
            $payment = SupplierPayment::create([
                'payment_ref' => $this->generatePaymentRef(),
                'supplier_id' => $request->supplier_id,
                'purchase_order_id' => $request->purchase_order_id,
                'po_amount' => $po->total_amount,
                'payment_amount' => $request->payment_amount,
                'payment_mode' => $request->payment_mode,
                'cheque_number' => $request->cheque_number,
                'cheque_date' => $request->cheque_date,
                'payment_date' => $request->payment_date,
                'status' => 'Pending',
                'notes' => $request->notes
            ]);

            DB::commit();

            return back()->with('success', 'Payment recorded successfully. Reference: ' . $payment->payment_ref);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Failed to save payment: ' . $e->getMessage());
        }
    }

    public function updateStatus(SupplierPayment $payment, $status)
    {
        $validStatuses = ['Pending', 'Cleared', 'Bounced'];

        if (!in_array($status, $validStatuses)) {
            return back()->with('error', 'Invalid status.');
        }

        $payment->update(['status' => $status]);

        return back()->with('success', 'Payment status updated to ' . $status);
    }

    public function destroy(SupplierPayment $payment)
    {
        if ($payment->status === 'Cleared') {
            return back()->with('error', 'Cannot delete cleared payments.');
        }

        $payment->delete();
        return back()->with('success', 'Payment deleted.');
    }

    // Supplier Ledger
    public function supplierLedger(Supplier $supplier)
    {
        $pos = $supplier->purchaseOrders()
            ->where('status', '!=', 'Pending')
            ->with('payments')
            ->latest()
            ->get();

        $totalPoValue = $supplier->getTotalPoValue();
        $totalPaid = $supplier->getTotalPaidAmount();
        $outstanding = $supplier->getOutstandingBalance();
        $overpayment = $supplier->getTotalOverpayment();

        $ledgerData = $pos->map(function ($po) {
            $totalPaid = $po->getTotalPaid();
            $remaining = $po->getRemainingBalance();

            return [
                'po_number' => $po->po_number,
                'po_date' => $po->order_date,
                'po_amount' => $po->total_amount,
                'paid_amount' => $totalPaid,
                'remaining_balance' => $remaining,
                'status' => $po->isFullyPaid() ? 'Paid' : 'Outstanding',
                'po' => $po
            ];
        });

        return view('supplier-payments.ledger', compact(
            'supplier',
            'ledgerData',
            'totalPoValue',
            'totalPaid',
            'outstanding',
            'overpayment'
        ));
    }

    // Get PO details for payment
    public function getPoDetails(PurchaseOrder $po)
    {
        $totalPaid = $po->getTotalPaid();
        $remaining = $po->getRemainingBalance();

        return response()->json([
            'po_number' => $po->po_number,
            'supplier_id' => $po->supplier_id,
            'supplier_name' => $po->supplier->name,
            'po_amount' => (float)$po->total_amount,
            'paid_amount' => (float)$totalPaid,
            'remaining_balance' => (float)$remaining,
            'order_date' => $po->order_date->format('Y-m-d')
        ]);
    }
}