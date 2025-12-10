<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'gas_type_id',
        'full_qty',
        'empty_qty'
    ];

    public function gasType()
    {
        return $this->belongsTo(GasType::class);
    }
}
