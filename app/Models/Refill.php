<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Refill extends Model
{
    use HasFactory;

    protected $fillable = [
        'refill_ref',
        'gas_type_id',
        'supplier_id',
        'cylinders_refilled',
        'refill_date',
        'cost_per_cylinder',
        'total_cost',
        'notes'
    ];

    protected $casts = [
        'refill_date' => 'date',
        'cost_per_cylinder' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function gasType()
    {
        return $this->belongsTo(GasType::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Get average cost per cylinder
    public function getAverageCostPerCylinder()
    {
        return $this->cost_per_cylinder;
    }

    // Calculate refill efficiency
    public function getEfficiency()
    {
        if ($this->cylinders_refilled == 0) return 0;
        return round(($this->cylinders_refilled / $this->cylinders_refilled) * 100, 2);
    }
}
