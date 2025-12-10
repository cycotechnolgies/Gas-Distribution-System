<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GrnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'grn_id',
        'gas_type_id',
        'ordered_qty',
        'received_qty',
        'damaged_qty'
    ];

    public function gasType()
    {
        return $this->belongsTo(GasType::class);
    }
}
