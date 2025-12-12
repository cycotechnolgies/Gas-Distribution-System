<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GrnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'grn_id',
        'gas_type_id',
        'ordered_qty',
        'received_qty',
        'damaged_qty',
        'rejected_qty',
        'rejection_notes'
    ];

    public function grn()
    {
        return $this->belongsTo(Grn::class);
    }

    public function gasType()
    {
        return $this->belongsTo(GasType::class);
    }

    // Calculate variance (received - ordered)
    public function getVariance()
    {
        return ($this->received_qty ?? 0) - ($this->ordered_qty ?? 0);
    }

    // Check if short supply
    public function isShortSupply()
    {
        $netReceived = ($this->received_qty ?? 0) - ($this->rejected_qty ?? 0);
        return $netReceived < ($this->ordered_qty ?? 0);
    }

    // Check if over delivery
    public function isOverDelivery()
    {
        $netReceived = ($this->received_qty ?? 0) - ($this->rejected_qty ?? 0);
        return $netReceived > ($this->ordered_qty ?? 0);
    }

    // Get short supply quantity
    public function getShortSupplyQty()
    {
        $netReceived = ($this->received_qty ?? 0) - ($this->rejected_qty ?? 0);
        $shortage = ($this->ordered_qty ?? 0) - $netReceived;
        return max(0, $shortage);
    }

    // Get over delivery quantity
    public function getOverDeliveryQty()
    {
        $netReceived = ($this->received_qty ?? 0) - ($this->rejected_qty ?? 0);
        $overage = $netReceived - ($this->ordered_qty ?? 0);
        return max(0, $overage);
    }

    // Get net received (received - rejected)
    public function getNetReceived()
    {
        return max(0, ($this->received_qty ?? 0) - ($this->rejected_qty ?? 0));
    }

    // Get total quality issues (damaged + rejected)
    public function getTotalQualityIssues()
    {
        return ($this->damaged_qty ?? 0) + ($this->rejected_qty ?? 0);
    }

    // Get percentage received
    public function getReceivedPercentage()
    {
        if ($this->ordered_qty == 0) return 0;
        return round((($this->received_qty ?? 0) / $this->ordered_qty) * 100, 2);
    }

    // Get percentage of quality issues
    public function getQualityIssuePercentage()
    {
        if ($this->received_qty == 0) return 0;
        return round(($this->getTotalQualityIssues() / $this->received_qty) * 100, 2);
    }
}

