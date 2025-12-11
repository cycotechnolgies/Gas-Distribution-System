<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GasTypeCustomerPrice extends Model
{
    use HasFactory;

    protected $table = 'gas_type_customer_prices';

    protected $fillable = [
        'customer_id',
        'gas_type_id',
        'custom_price',
        'notes'
    ];

    protected $casts = [
        'custom_price' => 'decimal:2'
    ];

    /**
     * Relationships
     */

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function gasType()
    {
        return $this->belongsTo(GasType::class);
    }

    /**
     * Scopes
     */

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeForGasType($query, $gasTypeId)
    {
        return $query->where('gas_type_id', $gasTypeId);
    }

    /**
     * Get custom price for customer and gas type
     *
     * @param int $customerId
     * @param int $gasTypeId
     * @return float|null
     */
    public static function getPrice($customerId, $gasTypeId)
    {
        $price = self::forCustomer($customerId)
            ->forGasType($gasTypeId)
            ->first();

        return $price ? $price->custom_price : null;
    }

    /**
     * Set or update custom price
     *
     * @param int $customerId
     * @param int $gasTypeId
     * @param float $price
     * @param string|null $notes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function setPrice($customerId, $gasTypeId, $price, $notes = null)
    {
        return self::updateOrCreate(
            [
                'customer_id' => $customerId,
                'gas_type_id' => $gasTypeId
            ],
            [
                'custom_price' => $price,
                'notes' => $notes
            ]
        );
    }

    /**
     * Remove custom price override
     *
     * @param int $customerId
     * @param int $gasTypeId
     * @return bool
     */
    public static function removePrice($customerId, $gasTypeId)
    {
        return self::forCustomer($customerId)
            ->forGasType($gasTypeId)
            ->delete();
    }
}
