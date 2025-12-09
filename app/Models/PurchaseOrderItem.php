<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'gas_type_id',
        'quantity',
        'unit_price',
        'total'
    ];

    public function gasType()
    {
        return $this->belongsTo(GasType::class);
    }
}
