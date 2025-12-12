<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grn extends Model
{
    use HasFactory;

    protected $fillable = [
        'grn_number',
        'supplier_id',
        'purchase_order_id',
        'received_date',
        'status',
        'approved',
        'approved_at',
        'variance_notes',
        'rejection_notes'
    ];

    protected $casts = [
        'received_date' => 'date',
        'approved_at' => 'datetime',
        'approved' => 'boolean'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items()
    {
        return $this->hasMany(GrnItem::class);
    }

    // Check if all quantities match
    public function hasVariance()
    {
        foreach ($this->items as $item) {
            if ($item->getVariance() != 0) {
                return true;
            }
        }
        return false;
    }

    // Get total variance amount
    public function getTotalVariance()
    {
        return $this->items->sum(function ($item) {
            return $item->getVariance();
        });
    }

    // Get total rejected quantity
    public function getTotalRejected()
    {
        return $this->items->sum(function ($item) {
            return $item->rejected_qty ?? 0;
        });
    }

    // Get total damaged quantity
    public function getTotalDamaged()
    {
        return $this->items->sum(function ($item) {
            return $item->damaged_qty ?? 0;
        });
    }

    // Check if there are short supplies
    public function hasShortSupply()
    {
        foreach ($this->items as $item) {
            if ($item->isShortSupply()) {
                return true;
            }
        }
        return false;
    }

    // Check if there are over-deliveries
    public function hasOverDelivery()
    {
        foreach ($this->items as $item) {
            if ($item->isOverDelivery()) {
                return true;
            }
        }
        return false;
    }

    // Get net received quantity (received - rejected)
    public function getNetReceivedForType($gasTypeId)
    {
        $item = $this->items->where('gas_type_id', $gasTypeId)->first();
        if (!$item) return 0;
        return max(0, ($item->received_qty ?? 0) - ($item->rejected_qty ?? 0));
    }

    // Can this GRN be approved?
    public function canBeApproved()
    {
        if ($this->approved) return false;
        if ($this->items->count() === 0) return false;
        return true;
    }

    // Get approval recommendations
    public function getApprovalIssues()
    {
        $issues = [];

        if ($this->hasShortSupply()) {
            $issues[] = 'Short supply detected on one or more items';
        }

        if ($this->getTotalRejected() > 0) {
            $issues[] = 'Total rejected: ' . $this->getTotalRejected() . ' units';
        }

        if ($this->getTotalDamaged() > 0) {
            $issues[] = 'Total damaged: ' . $this->getTotalDamaged() . ' units';
        }

        return $issues;
    }
}
