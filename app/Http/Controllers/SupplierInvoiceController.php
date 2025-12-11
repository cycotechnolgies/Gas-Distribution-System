<?php

namespace App\Http\Controllers;

use App\Models\{SupplierInvoice, Supplier, PurchaseOrder};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierInvoiceController extends Controller
{
    public function index()
    {
        $invoices = SupplierInvoice::with('supplier', 'purchaseOrder')
            ->latest()
            ->paginate(15);

        $suppliers = Supplier::all();
        $purchaseOrders = PurchaseOrder::all();

        return view('supplier-invoices.index', compact('invoices', 'suppliers', 'purchaseOrders'));
    }

    private function generateInvoiceNumber()
    {
        return 'INV-' . date('Ymd') . '-' . str_pad(SupplierInvoice::count() + 1, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'invoice_date' => 'required|date|before_or_equal:today',
            'invoice_amount' => 'required|numeric|min:0.01',
            'status' => 'required|in:Pending,Reconciled,Disputed',
            'description' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            // Validate supplier matches PO if provided
            if ($request->purchase_order_id) {
                $po = PurchaseOrder::find($request->purchase_order_id);
                if ($po->supplier_id != $request->supplier_id) {
                    return back()->with('error', 'Selected supplier does not match PO supplier.');
                }
            }

            $invoice = SupplierInvoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'supplier_id' => $request->supplier_id,
                'purchase_order_id' => $request->purchase_order_id,
                'invoice_date' => $request->invoice_date,
                'invoice_amount' => $request->invoice_amount,
                'status' => $request->status,
                'description' => $request->description,
                'notes' => $request->notes
            ]);

            return back()->with('success', 'Invoice recorded: ' . $invoice->invoice_number);
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Failed to save invoice: ' . $e->getMessage());
        }
    }

    public function updateStatus(SupplierInvoice $invoice, $status)
    {
        $validStatuses = ['Pending', 'Reconciled', 'Disputed'];

        if (!in_array($status, $validStatuses)) {
            return back()->with('error', 'Invalid status.');
        }

        $invoice->update(['status' => $status]);
        return back()->with('success', 'Invoice status updated to ' . $status);
    }

    public function destroy(SupplierInvoice $invoice)
    {
        if ($invoice->status === 'Reconciled') {
            return back()->with('error', 'Cannot delete reconciled invoices.');
        }

        $invoice->delete();
        return back()->with('success', 'Invoice deleted.');
    }

    // Get supplier invoice report
    public function supplierReport(Supplier $supplier)
    {
        $invoices = $supplier->invoices()
            ->with('purchaseOrder')
            ->latest()
            ->get();

        $matchedInvoices = $supplier->getMatchedInvoices();
        $unmatchedInvoices = $supplier->getUnmatchedInvoices();
        $statusSummary = $supplier->getInvoiceStatusSummary();

        $totalInvoiced = $supplier->getTotalInvoiceAmount();
        $totalPo = $supplier->getTotalPoValue();
        $variance = $supplier->getTotalInvoiceVariance();
        $variancePercentage = round(($variance / $totalPo * 100), 2);

        return view('supplier-invoices.report', compact(
            'supplier',
            'invoices',
            'matchedInvoices',
            'unmatchedInvoices',
            'statusSummary',
            'totalInvoiced',
            'totalPo',
            'variance',
            'variancePercentage'
        ));
    }
}
