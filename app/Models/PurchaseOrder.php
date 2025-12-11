<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'supplier_id',
        'order_date',
        'status',
        'total_amount',
        'delivery_date',
        'notes'
    ];

    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function grns()
    {
        return $this->hasMany(Grn::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    // Get received quantity for tracking
    public function getReceivedQuantity($gasTypeId)
    {
        return $this->grns()
            ->where('approved', true)
            ->whereHas('items', function ($q) use ($gasTypeId) {
                $q->where('gas_type_id', $gasTypeId);
            })
            ->with('items')
            ->get()
            ->reduce(function ($carry, $grn) use ($gasTypeId) {
                $item = $grn->items->where('gas_type_id', $gasTypeId)->first();
                return $carry + ($item ? ($item->received_qty - ($item->rejected_qty ?? 0)) : 0);
            }, 0);
    }

    // Get total amount paid for this PO
    public function getTotalPaid()
    {
        return $this->payments()
            ->whereIn('status', ['Cleared', 'Pending'])
            ->sum('payment_amount') ?? 0;
    }

    // Get remaining balance for this PO
    public function getRemainingBalance()
    {
        return max(0, $this->total_amount - $this->getTotalPaid());
    }

    // Check if fully paid
    public function isFullyPaid()
    {
        return $this->getRemainingBalance() <= 0;
    }

    // Check if PO is fully received
    public function isFullyReceived()
    {
        foreach ($this->items as $item) {
            if ($this->getReceivedQuantity($item->gas_type_id) < $item->quantity) {
                return false;
            }
        }
        return true;
    }

    // Check if PO is partially received
    public function isPartiallyReceived()
    {
        $hasReceived = false;
        foreach ($this->items as $item) {
            $received = $this->getReceivedQuantity($item->gas_type_id);
            if ($received > 0) {
                $hasReceived = true;
            }
            if ($received < $item->quantity) {
                return $hasReceived;
            }
        }
        return false;
    }
}
