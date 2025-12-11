<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryRoute extends Model
{
    protected $fillable = [
        'name',
        'description',
        'driver_id',
        'assistant_id',
        'vehicle_id',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function assistant()
    {
        return $this->belongsTo(Assistant::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
