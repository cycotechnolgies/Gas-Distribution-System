<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_number',
        'model',
        'type',
        'capacity',
        'status',
        'total_deliveries',
        'total_km',
        'fuel_consumption',
        'last_maintenance_date',
        'next_maintenance_due',
        'registration_expiry',
        'purchase_date',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'last_maintenance_date' => 'date',
        'next_maintenance_due' => 'date',
        'registration_expiry' => 'date',
        'fuel_consumption' => 'decimal:2',
    ];

    // ========== RELATIONSHIPS ==========

    public function deliveryRoutes(): HasMany
    {
        return $this->hasMany(DeliveryRoute::class, 'vehicle_id');
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
     * Check if vehicle is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if vehicle is in maintenance
     */
    public function isInMaintenance(): bool
    {
        return $this->status === 'maintenance';
    }

    /**
     * Check if vehicle is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Mark vehicle as in maintenance
     */
    public function markMaintenance(Carbon $dueDate): self
    {
        $this->status = 'maintenance';
        $this->last_maintenance_date = now();
        $this->next_maintenance_due = $dueDate;
        $this->save();
        return $this;
    }

    /**
     * Mark vehicle as active after maintenance
     */
    public function markActiveAfterMaintenance(): self
    {
        $this->status = 'active';
        $this->save();
        return $this;
    }

    /**
     * Toggle vehicle status between active/inactive
     */
    public function toggleStatus(): self
    {
        if ($this->isActive()) {
            $this->status = 'inactive';
        } elseif ($this->isInactive()) {
            $this->status = 'active';
        }
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
     * Get all completed routes for this vehicle
     */
    public function getCompletedRoutes()
    {
        return $this->deliveryRoutes()
            ->where('route_status', 'Completed')
            ->get();
    }

    /**
     * Record a completed delivery
     */
    public function recordDelivery(int $km = 0): self
    {
        $this->increment('total_deliveries');
        if ($km > 0) {
            $this->increment('total_km', $km);
        }
        return $this;
    }

    /**
     * Get vehicle age in years
     */
    public function getAgeYears(): ?int
    {
        if (!$this->purchase_date) {
            return null;
        }
        return $this->purchase_date->diffInYears(now());
    }

    /**
     * Check if registration is expiring soon (within 30 days)
     */
    public function isRegistrationExpiringSoon(): bool
    {
        if (!$this->registration_expiry) {
            return false;
        }
        return $this->registration_expiry->diffInDays(now()) <= 30;
    }

    /**
     * Check if maintenance is due soon (within 30 days)
     */
    public function isMaintenanceDueSoon(): bool
    {
        if (!$this->next_maintenance_due) {
            return false;
        }
        return $this->next_maintenance_due->diffInDays(now()) <= 30;
    }

    /**
     * Check if maintenance is overdue
     */
    public function isMaintenanceOverdue(): bool
    {
        if (!$this->next_maintenance_due) {
            return false;
        }
        return $this->next_maintenance_due->isPast();
    }

    /**
     * Get maintenance history summary
     */
    public function getMaintenanceStatus(): array
    {
        return [
            'last_maintenance' => $this->last_maintenance_date?->format('d M Y'),
            'next_due' => $this->next_maintenance_due?->format('d M Y'),
            'is_overdue' => $this->isMaintenanceOverdue(),
            'days_until_due' => $this->next_maintenance_due ? $this->next_maintenance_due->diffInDays(now()) : null,
        ];
    }

    /**
     * Get vehicle performance metrics
     */
    public function getPerformanceMetrics(): array
    {
        return [
            'total_deliveries' => $this->total_deliveries,
            'total_km' => $this->total_km,
            'fuel_consumption' => $this->fuel_consumption,
            'avg_km_per_delivery' => $this->total_deliveries > 0 ? round($this->total_km / $this->total_deliveries, 2) : 0,
            'estimated_fuel_cost' => $this->fuel_consumption > 0 && $this->total_km > 0 
                ? round($this->total_km / $this->fuel_consumption, 2) 
                : 0,
        ];
    }

    /**
     * Get vehicle health status
     */
    public function getHealthStatus(): string
    {
        if ($this->isInMaintenance()) return 'Maintenance';
        if ($this->isMaintenanceOverdue()) return 'Overdue Maintenance';
        if ($this->isMaintenanceDueSoon()) return 'Maintenance Due Soon';
        if ($this->isRegistrationExpiringSoon()) return 'Registration Expiring';
        return 'Good';
    }

    /**
     * Check if vehicle can be deleted
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
     * Get summary statistics
     */
    public static function getStatistics(): array
    {
        return [
            'total_vehicles' => self::count(),
            'active_vehicles' => self::where('status', 'active')->count(),
            'inactive_vehicles' => self::where('status', 'inactive')->count(),
            'in_maintenance' => self::where('status', 'maintenance')->count(),
            'total_deliveries' => self::sum('total_deliveries'),
            'total_km' => self::sum('total_km'),
        ];
    }

    /**
     * Get vehicles requiring attention
     */
    public static function getNeedsAttention()
    {
        $vehicles = self::all();
        $needsAttention = [];

        foreach ($vehicles as $vehicle) {
            if ($vehicle->isMaintenanceOverdue() || $vehicle->isRegistrationExpiringSoon()) {
                $needsAttention[] = $vehicle;
            }
        }

        return $needsAttention;
    }
}
