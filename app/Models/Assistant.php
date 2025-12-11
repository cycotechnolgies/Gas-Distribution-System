<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assistant extends Model
{
    use HasFactory;

    protected $fillable = ['name','phone','status','notes'];

    // public function deliveryRoutes()
    // {
    //     return $this->hasMany(DeliveryRoute::class, 'assistant_id');
    // }
}
