<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email'
    ];

    public function gasTypes()
    {
        return $this->belongsToMany(\App\Models\GasType::class)
            ->withPivot('rate')
            ->withTimestamps();
    }

}
