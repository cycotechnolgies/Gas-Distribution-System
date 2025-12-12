<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class DeliveryRoute extends Model
{
    protected $fillable = [
        'route_name',
        'route_date',
        'driver_id',
        'assistant_id',
        'vehicle_id',
        'route_status',
        'actual_start_time',
        'actual_end_time',
        'notes',
    ];

    protected $casts = [
        'route_date' => 'date',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function assistant(): BelongsTo
    {
        return $this->belongsTo(Assistant::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function stops(): HasMany
    {
        return $this->hasMany(RouteStop::class, 'delivery_route_id')->orderBy('stop_order');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'delivery_route_id');
    }

    // ========== METHODS ==========

    /**
     * Add a stop to this route
     */
    public function addStop($customerId, $orderId = null, $plannedTime = null, $stopOrder = null): RouteStop
    {
        // Determine stop order
        if ($stopOrder === null) {
            $maxOrder = $this->stops()->max('stop_order') ?? 0;
            $stopOrder = $maxOrder + 1;
        }

        return $this->stops()->create([
            'customer_id' => $customerId,
            'order_id' => $orderId,
            'stop_order' => $stopOrder,
            'planned_time' => $plannedTime,
        ]);
    }

    /**
     * Remove a stop from this route
     */
    public function removeStop($stopId): bool
    {
        $stop = $this->stops()->find($stopId);
        if ($stop) {
            $stop->delete();
            // Reorder remaining stops
            $this->reorderStops();
            return true;
        }
        return false;
    }

    /**
     * Reorder stops sequentially after a stop is removed
     */
    private function reorderStops(): void
    {
        $stops = $this->stops()->get();
        foreach ($stops as $index => $stop) {
            $stop->update(['stop_order' => $index + 1]);
        }
    }

    /**
     * Get all stops for this route
     */
    public function getStops()
    {
        return $this->stops()->get();
    }

    /**
     * Get pending stops (not yet delivered)
     */
    public function getPendingStops()
    {
        return $this->stops()->whereNull('actual_time')->get();
    }

    /**
     * Get completed stops
     */
    public function getCompletedStops()
    {
        return $this->stops()->whereNotNull('actual_time')->get();
    }

    /**
     * Get pending stops count
     */
    public function getPendingStopsCount(): int
    {
        return $this->stops()->whereNull('actual_time')->count();
    }

    /**
     * Check if this route can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Cannot delete if route is in progress or completed
        if ($this->route_status === 'InProgress' || $this->route_status === 'Completed') {
            return false;
        }
        
        return true;
    }

    /**
     * Mark route as in progress
     */
    public function markInProgress(): self
    {
        if ($this->canTransitionTo('InProgress')) {
            $this->update([
                'route_status' => 'InProgress',
                'actual_start_time' => now(),
            ]);
        }
        return $this;
    }

    /**
     * Mark route as completed
     */
    public function markCompleted(): self
    {
        if ($this->canTransitionTo('Completed')) {
            $this->update([
                'route_status' => 'Completed',
                'actual_end_time' => now(),
            ]);
        }
        return $this;
    }

    /**
     * Check if transition to a status is allowed
     */
    public function canTransitionTo(string $targetStatus): bool
    {
        $allowedTransitions = [
            'Planned' => ['InProgress', 'Cancelled'],
            'InProgress' => ['Completed', 'Cancelled'],
            'Completed' => [],
            'Cancelled' => [],
        ];

        return in_array($targetStatus, $allowedTransitions[$this->route_status] ?? []);
    }

    /**
     * Calculate total route duration (actual vs planned)
     */
    public function calculateDuration(): ?int
    {
        if (!$this->actual_start_time || !$this->actual_end_time) {
            return null;
        }

        return $this->actual_start_time->diffInMinutes($this->actual_end_time);
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentage(): int
    {
        $totalStops = $this->stops()->count();
        if ($totalStops === 0) {
            return 0;
        }

        $completedStops = $this->stops()->whereNotNull('actual_time')->count();
        return (int) (($completedStops / $totalStops) * 100);
    }

    /**
     * Get route statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_stops' => $this->stops()->count(),
            'completed_stops' => $this->stops()->whereNotNull('actual_time')->count(),
            'pending_stops' => $this->getPendingStopsCount(),
            'completion_percentage' => $this->getCompletionPercentage(),
            'duration_minutes' => $this->calculateDuration(),
        ];
    }

    /**
     * Get late deliveries count
     */
    public function getLateDeliveriesCount(): int
    {
        $stops = $this->stops()->whereNotNull('actual_time')->get();
        $lateCount = 0;

        foreach ($stops as $stop) {
            $diff = $stop->calculateTimeDifference();
            if ($diff !== null && $diff > 0) {
                $lateCount++;
            }
        }

        return $lateCount;
    }

    /**
     * Check if all stops are completed
     */
    public function isFullyCompleted(): bool
    {
        return $this->getPendingStopsCount() === 0;
    }
}
