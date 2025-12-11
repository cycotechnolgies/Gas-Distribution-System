<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = ['vehicle_number','model','capacity','status','notes'];

    // public function deliveryRoutes()
    // {
    //     return $this->hasMany(DeliveryRoute::class, 'vehicle_id');
    // }
}
