<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GasType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price'
    ];

    public function suppliers()
    {
        return $this->belongsToMany(\App\Models\Supplier::class)
            ->withPivot('rate')
            ->withTimestamps();
    }

    public function stock()
    {
        return $this->hasOne(\App\Models\Stock::class, 'gas_type_id');
    }
}
