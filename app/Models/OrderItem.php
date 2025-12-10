<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'gas_type_id',
        'quantity',
        'unit_price',
        'total',
        'delivered_adjusted'
    ];

    public function gasType() { return $this->belongsTo(GasType::class); }
    public function order() { return $this->belongsTo(Order::class); }
}
