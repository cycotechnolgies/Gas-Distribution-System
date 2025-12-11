<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = ['name','phone','license_number','status','notes'];

    // if you later add DeliveryRoute model:
    // public function deliveryRoutes()
    // {
    //     return $this->hasMany(DeliveryRoute::class, 'driver_id');
    // }
}
