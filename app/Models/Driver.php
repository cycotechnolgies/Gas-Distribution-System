<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'license_number',
        'status',
        'total_deliveries',
        'on_time_deliveries',
        'average_rating',
        'address',
        'hire_date',
        'notes',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'average_rating' => 'decimal:2',
    ];

    // ========== RELATIONSHIPS ==========

    public function deliveryRoutes(): HasMany
    {
        return $this->hasMany(DeliveryRoute::class, 'driver_id');
    }

    public function currentRoute()
    {
        return $this->deliveryRoutes()
            ->where('route_status', 'InProgress')
            ->latest()
            ->first();
    }

    // ========== METHODS ==========

    /**
     * Check if driver is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if driver is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Toggle driver status
     */
    public function toggleStatus(): self
    {
        $this->status = $this->isActive() ? 'inactive' : 'active';
        $this->save();
        return $this;
    }

    /**
     * Get current assigned route
     */
    public function getAssignedRoute(): ?DeliveryRoute
    {
        return $this->currentRoute();
    }

    /**
     * Get all completed routes for this driver
     */
    public function getCompletedRoutes()
    {
        return $this->deliveryRoutes()
            ->where('route_status', 'Completed')
            ->get();
    }

    /**
     * Get total deliveries count
     */
    public function getTotalDeliveries(): int
    {
        return $this->total_deliveries;
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics(): array
    {
        $onTimePercentage = $this->total_deliveries > 0 
            ? (($this->on_time_deliveries / $this->total_deliveries) * 100)
            : 0;

        return [
            'total_deliveries' => $this->total_deliveries,
            'on_time_deliveries' => $this->on_time_deliveries,
            'late_deliveries' => $this->total_deliveries - $this->on_time_deliveries,
            'on_time_percentage' => round($onTimePercentage, 2),
            'average_rating' => $this->average_rating,
        ];
    }

    /**
     * Record a completed delivery
     */
    public function recordDelivery(bool $onTime = true): self
    {
        $this->increment('total_deliveries');
        if ($onTime) {
            $this->increment('on_time_deliveries');
        }
        return $this;
    }

    /**
     * Update driver rating
     */
    public function updateRating(float $newRating): self
    {
        // Calculate weighted average
        $totalRatings = $this->total_deliveries;
        if ($totalRatings > 0) {
            $currentTotal = $this->average_rating * $totalRatings;
            $newTotal = $currentTotal + $newRating;
            $this->average_rating = $newTotal / ($totalRatings + 1);
        } else {
            $this->average_rating = $newRating;
        }
        $this->save();
        return $this;
    }

    /**
     * Check if driver can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Cannot delete if has active routes
        $activeRoutes = $this->deliveryRoutes()
            ->where('route_status', '!=', 'Completed')
            ->where('route_status', '!=', 'Cancelled')
            ->count();

        return $activeRoutes === 0;
    }

    /**
     * Get experience in years
     */
    public function getExperienceYears(): ?int
    {
        if (!$this->hire_date) {
            return null;
        }
        return $this->hire_date->diffInYears(now());
    }

    /**
     * Get on-time performance rating
     */
    public function getPerformanceRating(): string
    {
        $metrics = $this->getPerformanceMetrics();
        $percentage = $metrics['on_time_percentage'];

        if ($percentage >= 95) return 'Excellent';
        if ($percentage >= 85) return 'Very Good';
        if ($percentage >= 75) return 'Good';
        if ($percentage >= 60) return 'Fair';
        return 'Needs Improvement';
    }

    /**
     * Get drivers available for a specific date (not already assigned)
     */
    public static function getAvailableForDate($date)
    {
        return self::where('status', 'active')
            ->whereDoesntHave('deliveryRoutes', function ($query) use ($date) {
                $query->where('route_date', $date)
                      ->where('route_status', '!=', 'Cancelled');
            })
            ->get();
    }

    /**
     * Get summary statistics
     */
    public static function getStatistics(): array
    {
        return [
            'total_drivers' => self::count(),
            'active_drivers' => self::where('status', 'active')->count(),
            'inactive_drivers' => self::where('status', 'inactive')->count(),
            'total_deliveries' => self::sum('total_deliveries'),
            'average_rating' => round(self::avg('average_rating'), 2),
        ];
    }
}
