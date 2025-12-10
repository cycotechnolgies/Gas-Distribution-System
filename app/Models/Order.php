<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'delivery_route_id',
        'order_date',
        'status',
        'urgent',
        'total_amount'
    ];

    protected $casts = [
        'urgent' => 'boolean',
        'order_date' => 'date',
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    // public function deliveryRoute() { return $this->belongsTo(DeliveryRoute::class); }
}
