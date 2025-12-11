<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_ref',
        'supplier_id',
        'purchase_order_id',
        'po_amount',
        'payment_amount',
        'payment_mode',
        'cheque_number',
        'cheque_date',
        'payment_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'cheque_date' => 'date',
        'po_amount' => 'decimal:2',
        'payment_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    // Get remaining balance for this PO
    public function getRemainingBalance()
    {
        return max(0, $this->po_amount - $this->payment_amount);
    }

    // Check if overpaid
    public function isOverpaid()
    {
        return $this->payment_amount > $this->po_amount;
    }

    // Get overpayment amount
    public function getOverpaymentAmount()
    {
        return $this->isOverpaid() ? ($this->payment_amount - $this->po_amount) : 0;
    }
}