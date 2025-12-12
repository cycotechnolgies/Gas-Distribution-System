<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierReportController extends Controller
{
    public function dashboard(Supplier $supplier)
    {
        // Financial Summary
        $totalPoValue = $supplier->getTotalPoValue();
        $totalPaid = $supplier->getTotalPaidAmount();
        $outstanding = $supplier->getOutstandingBalance();
        $overpaid = $supplier->getTotalOverpayment();

        // Invoice Summary
        $totalInvoiced = $supplier->getTotalInvoiceAmount();
        $invoiceVariance = $supplier->getTotalInvoiceVariance();
        $statusSummary = $supplier->getInvoiceStatusSummary();

        // Refill Summary
        $totalRefillCost = $supplier->getTotalRefillsCost();
        $totalCylindersRefilled = $supplier->getTotalCylindersRefilled();
        $refillsByType = $supplier->getRefillsByType();

        // PO Summary
        $completionRate = $supplier->getCompletionRate();
        $totalPos = $supplier->purchaseOrders()->count();
        $completedPos = $supplier->purchaseOrders()->where('status', 'Completed')->count();
        $partialPos = $supplier->purchaseOrders()->where('status', 'Partial')->count();
        $pendingPos = $supplier->purchaseOrders()->where('status', 'Pending')->count();

        // Cylinder Tracking
        $totalCylindersReceived = $supplier->getTotalCylindersReceived();

        // Payment Performance
        $averagePaymentDays = $supplier->getAveragePaymentDays();

        // Recent Activity
        $recentPos = $supplier->purchaseOrders()
            ->with('items.gasType')
            ->latest()
            ->take(5)
            ->get();

        $recentPayments = $supplier->payments()
            ->with('purchaseOrder')
            ->latest()
            ->take(5)
            ->get();

        $recentRefills = $supplier->refills()
            ->with('gasType')
            ->latest()
            ->take(5)
            ->get();

        return view('suppliers.dashboard', compact(
            'supplier',
            'totalPoValue',
            'totalPaid',
            'outstanding',
            'overpaid',
            'totalInvoiced',
            'invoiceVariance',
            'statusSummary',
            'totalRefillCost',
            'totalCylindersRefilled',
            'refillsByType',
            'completionRate',
            'totalPos',
            'completedPos',
            'partialPos',
            'pendingPos',
            'totalCylindersReceived',
            'averagePaymentDays',
            'recentPos',
            'recentPayments',
            'recentRefills'
        ));
    }

    public function poVsInvoiceComparison(Supplier $supplier)
    {
        $pos = $supplier->purchaseOrders()
            ->with('items.gasType', 'payments')
            ->get();

        $invoices = $supplier->invoices()
            ->with('purchaseOrder')
            ->get();

        // Build comparison data
        $comparisonData = $pos->map(function ($po) {
            $linkedInvoice = $po->invoices()->first();
            
            return [
                'po_number' => $po->po_number,
                'po_date' => $po->order_date,
                'po_amount' => $po->total_amount,
                'po_status' => $po->status,
                'invoice_number' => $linkedInvoice ? $linkedInvoice->invoice_number : 'N/A',
                'invoice_date' => $linkedInvoice ? $linkedInvoice->invoice_date : null,
                'invoice_amount' => $linkedInvoice ? $linkedInvoice->invoice_amount : 0,
                'invoice_status' => $linkedInvoice ? $linkedInvoice->status : 'Not Linked',
                'variance' => $linkedInvoice ? ($linkedInvoice->invoice_amount - $po->total_amount) : null,
                'matched' => $linkedInvoice ? $linkedInvoice->isMatched() : false,
                'total_paid' => $po->getTotalPaid(),
                'remaining_balance' => $po->getRemainingBalance(),
                'po' => $po,
                'invoice' => $linkedInvoice
            ];
        })->sortByDesc('po_date');

        $totalPos = $comparisonData->count();
        $matchedInvoices = $comparisonData->filter(function ($item) {
            return $item['matched'];
        })->count();
        $unmatchedPos = $totalPos - $matchedInvoices;

        $totalPoAmount = $comparisonData->sum('po_amount');
        $totalInvoiceAmount = $comparisonData->sum('invoice_amount');
        $totalVariance = $totalInvoiceAmount - $totalPoAmount;

        return view('suppliers.po-vs-invoice', compact(
            'supplier',
            'comparisonData',
            'totalPos',
            'matchedInvoices',
            'unmatchedPos',
            'totalPoAmount',
            'totalInvoiceAmount',
            'totalVariance'
        ));
    }

    public function refillAnalysis(Supplier $supplier)
    {
        $refillsByType = $supplier->getRefillsByType();

        $refills = $supplier->refills()
            ->with('gasType')
            ->latest()
            ->get();

        $monthlyRefills = $refills->groupBy(function ($refill) {
            return $refill->refill_date->format('Y-m');
        })->map(function ($items) {
            return [
                'month' => $items->first()->refill_date->format('F Y'),
                'cylinders' => $items->sum('cylinders_refilled'),
                'cost' => $items->sum('total_cost'),
                'average_cost' => $items->avg('cost_per_cylinder')
            ];
        })->sortByDesc('month');

        $totalRefills = $refills->count();
        $totalCylinders = $refills->sum('cylinders_refilled');
        $totalCost = $refills->sum('total_cost');
        $averageCostPerCylinder = $refills->avg('cost_per_cylinder');

        return view('suppliers.refill-analysis', compact(
            'supplier',
            'refillsByType',
            'refills',
            'monthlyRefills',
            'totalRefills',
            'totalCylinders',
            'totalCost',
            'averageCostPerCylinder'
        ));
    }

    public function paymentHistory(Supplier $supplier)
    {
        $payments = $supplier->payments()
            ->with('purchaseOrder')
            ->latest()
            ->get();

        $totalPayments = $payments->count();
        $totalPaid = $payments->sum('payment_amount');
        $averagePayment = $payments->avg('payment_amount');
        $averageDays = $supplier->getAveragePaymentDays();

        $paymentsByMode = $payments->groupBy('payment_mode')->map(function ($items) {
            return [
                'mode' => $items->first()->payment_mode,
                'count' => $items->count(),
                'total' => $items->sum('payment_amount')
            ];
        });

        $paymentsByStatus = $payments->groupBy('status')->map(function ($items) {
            return [
                'status' => $items->first()->status,
                'count' => $items->count(),
                'total' => $items->sum('payment_amount')
            ];
        });

        return view('suppliers.payment-history', compact(
            'supplier',
            'payments',
            'totalPayments',
            'totalPaid',
            'averagePayment',
            'averageDays',
            'paymentsByMode',
            'paymentsByStatus'
        ));
    }
}
