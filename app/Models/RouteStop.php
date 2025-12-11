<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class RouteStop extends Model
{
    protected $table = 'route_stops';
    
    protected $fillable = [
        'delivery_route_id',
        'customer_id',
        'order_id',
        'stop_order',
        'planned_time',
        'actual_time',
        'notes',
    ];

    protected $casts = [
        'actual_time' => 'datetime',
        'planned_time' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========

    public function deliveryRoute(): BelongsTo
    {
        return $this->belongsTo(DeliveryRoute::class, 'delivery_route_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ========== METHODS ==========

    /**
     * Update the actual delivery time for this stop
     */
    public function updateActualTime($timestamp = null): self
    {
        $this->actual_time = $timestamp ?? now();
        $this->save();
        return $this;
    }

    /**
     * Calculate the time difference between planned and actual delivery
     * Returns positive if late, negative if early, null if not yet delivered
     */
    public function calculateTimeDifference(): ?int
    {
        if (!$this->actual_time || !$this->planned_time) {
            return null;
        }

        $planned = Carbon::parse($this->planned_time);
        $actual = Carbon::parse($this->actual_time);

        return $actual->diffInMinutes($planned);
    }

    /**
     * Check if this stop has been completed (actual time recorded)
     */
    public function isCompleted(): bool
    {
        return $this->actual_time !== null;
    }

    /**
     * Get human-readable delivery status for this stop
     */
    public function getDeliveryStatus(): string
    {
        if (!$this->isCompleted()) {
            return 'Pending';
        }

        $diff = $this->calculateTimeDifference();
        if ($diff === null) {
            return 'Pending';
        }

        if ($diff > 0) {
            return "Late ({$diff}m)";
        } elseif ($diff < 0) {
            return "Early (" . abs($diff) . "m)";
        } else {
            return 'On Time';
        }
    }

    /**
     * Get formatted planned time display
     */
    public function getPlannedTimeFormatted(): string
    {
        return $this->planned_time ? $this->planned_time->format('H:i') : 'Not set';
    }

    /**
     * Get formatted actual time display
     */
    public function getActualTimeFormatted(): string
    {
        return $this->actual_time ? $this->actual_time->format('d M Y H:i') : 'Not delivered';
    }

    /**
     * Check if delivery time variance exceeded acceptable threshold
     */
    public function hasVariance($thresholdMinutes = 30): bool
    {
        $diff = $this->calculateTimeDifference();
        if ($diff === null) {
            return false;
        }
        return abs($diff) > $thresholdMinutes;
    }
}
