<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assistant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'status',
        'total_deliveries',
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
        return $this->hasMany(DeliveryRoute::class, 'assistant_id');
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
     * Check if assistant is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if assistant is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Toggle assistant status
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
     * Get all completed routes for this assistant
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
        return [
            'total_deliveries' => $this->total_deliveries,
            'average_rating' => $this->average_rating,
        ];
    }

    /**
     * Record a completed delivery
     */
    public function recordDelivery(): self
    {
        $this->increment('total_deliveries');
        return $this;
    }

    /**
     * Update assistant rating
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
     * Check if assistant can be deleted
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
     * Get summary statistics
     */
    public static function getStatistics(): array
    {
        return [
            'total_assistants' => self::count(),
            'active_assistants' => self::where('status', 'active')->count(),
            'inactive_assistants' => self::where('status', 'inactive')->count(),
            'total_deliveries' => self::sum('total_deliveries'),
            'average_rating' => round(self::avg('average_rating'), 2),
        ];
    }
}
