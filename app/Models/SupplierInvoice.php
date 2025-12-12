<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'supplier_id',
        'purchase_order_id',
        'invoice_date',
        'invoice_amount',
        'status',
        'description',
        'notes'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'invoice_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    // Check if invoice matches PO amount
    public function isMatched()
    {
        if (!$this->purchaseOrder) return false;
        return $this->invoice_amount == $this->purchaseOrder->total_amount;
    }

    // Get variance between invoice and PO
    public function getVariance()
    {
        if (!$this->purchaseOrder) return $this->invoice_amount;
        return $this->invoice_amount - $this->purchaseOrder->total_amount;
    }

    // Check if over-invoiced
    public function isOverInvoiced()
    {
        return $this->getVariance() > 0;
    }

    // Check if under-invoiced
    public function isUnderInvoiced()
    {
        return $this->getVariance() < 0;
    }

    // Get variance percentage
    public function getVariancePercentage()
    {
        if (!$this->purchaseOrder || $this->purchaseOrder->total_amount == 0) {
            return 0;
        }
        return round(($this->getVariance() / $this->purchaseOrder->total_amount) * 100, 2);
    }
}
