<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email'
    ];

    public function gasTypes()
    {
        return $this->belongsToMany(\App\Models\GasType::class)
            ->withPivot('rate')
            ->withTimestamps();
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function refills()
    {
        return $this->hasMany(Refill::class);
    }

    public function invoices()
    {
        return $this->hasMany(SupplierInvoice::class);
    }

    // Get total PO value
    public function getTotalPoValue()
    {
        return $this->purchaseOrders()
            ->where('status', '!=', 'Pending')
            ->sum('total_amount') ?? 0;
    }

    // Get total paid amount
    public function getTotalPaidAmount()
    {
        return $this->payments()
            ->whereIn('status', ['Cleared', 'Pending'])
            ->sum('payment_amount') ?? 0;
    }

    // Get outstanding balance
    public function getOutstandingBalance()
    {
        return max(0, $this->getTotalPoValue() - $this->getTotalPaidAmount());
    }

    // Get total overpayment
    public function getTotalOverpayment()
    {
        $totalPaid = $this->getTotalPaidAmount();
        $totalDue = $this->getTotalPoValue();
        return $totalPaid > $totalDue ? ($totalPaid - $totalDue) : 0;
    }

    // Refill Tracking Methods
    public function getTotalRefillsCost()
    {
        return $this->refills()->sum('total_cost') ?? 0;
    }

    public function getTotalCylindersRefilled()
    {
        return $this->refills()->sum('cylinders_refilled') ?? 0;
    }

    // Get refills by gas type
    public function getRefillsByType()
    {
        return $this->refills()
            ->with('gasType')
            ->get()
            ->groupBy('gas_type_id')
            ->map(function ($items) {
                return [
                    'gas_type' => $items->first()->gasType->name,
                    'total_cylinders' => $items->sum('cylinders_refilled'),
                    'total_cost' => $items->sum('total_cost'),
                    'average_cost' => $items->avg('cost_per_cylinder'),
                    'refill_count' => $items->count()
                ];
            });
    }

    // Invoice Tracking Methods
    public function getTotalInvoiceAmount()
    {
        return $this->invoices()->sum('invoice_amount') ?? 0;
    }

    public function getTotalInvoiceVariance()
    {
        $totalInvoiced = $this->getTotalInvoiceAmount();
        $totalPoAmount = $this->getTotalPoValue();
        return $totalInvoiced - $totalPoAmount;
    }

    // Get invoice status summary
    public function getInvoiceStatusSummary()
    {
        return [
            'pending' => $this->invoices()->where('status', 'Pending')->count(),
            'reconciled' => $this->invoices()->where('status', 'Reconciled')->count(),
            'disputed' => $this->invoices()->where('status', 'Disputed')->count(),
        ];
    }

    // Get matched vs unmatched invoices
    public function getMatchedInvoices()
    {
        return $this->invoices()
            ->with('purchaseOrder')
            ->get()
            ->filter(function ($invoice) {
                return $invoice->isMatched();
            });
    }

    public function getUnmatchedInvoices()
    {
        return $this->invoices()
            ->with('purchaseOrder')
            ->get()
            ->filter(function ($invoice) {
                return !$invoice->isMatched();
            });
    }

    // Cylinder Tracking
    public function getTotalCylindersReceived()
    {
        return $this->purchaseOrders()
            ->sum('received_count') ?? 0;
    }

    // Get PO completion rate
    public function getCompletionRate()
    {
        $totalPos = $this->purchaseOrders()->count();
        if ($totalPos == 0) return 0;

        $completedPos = $this->purchaseOrders()
            ->where('status', 'Completed')
            ->count();

        return round(($completedPos / $totalPos) * 100, 2);
    }

    // Get payment timeliness (average days to pay)
    public function getAveragePaymentDays()
    {
        $payments = $this->payments()
            ->with('purchaseOrder')
            ->get();

        if ($payments->count() == 0) return 0;

        $totalDays = 0;
        foreach ($payments as $payment) {
            if ($payment->purchaseOrder) {
                $days = $payment->purchaseOrder->order_date->diffInDays($payment->payment_date);
                $totalDays += $days;
            }
        }

        return round($totalDays / $payments->count(), 0);
    }
}
