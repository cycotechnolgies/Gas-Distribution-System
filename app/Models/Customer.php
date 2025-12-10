<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'nic',
        'city',
        'customer_type',
        'credit_limit'
    ];

    // public function orders()
    // {
    //     return $this->hasMany(Order::class);
    // }
}

