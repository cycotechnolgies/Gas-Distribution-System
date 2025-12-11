<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPricingTier extends Model
{
    use HasFactory;

    protected $table = 'customer_pricing_tiers';

    protected $fillable = [
        'customer_type',
        'gas_type_id',
        'price',
        'description'
    ];

    protected $casts = [
        'price' => 'decimal:2'
    ];

    /**
     * Relationships
     */

    public function gasType()
    {
        return $this->belongsTo(GasType::class);
    }

    /**
     * Get pricing tier for a specific customer type and gas type
     *
     * @param string $customerType
     * @param int $gasTypeId
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public static function getPrice($customerType, $gasTypeId)
    {
        return self::where('customer_type', $customerType)
            ->where('gas_type_id', $gasTypeId)
            ->first();
    }

    /**
     * Get all prices for a customer type
     *
     * @param string $customerType
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPricesByType($customerType)
    {
        return self::where('customer_type', $customerType)
            ->with('gasType')
            ->get();
    }

    /**
     * Get all prices for a gas type
     *
     * @param int $gasTypeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPricesByGasType($gasTypeId)
    {
        return self::where('gas_type_id', $gasTypeId)
            ->get();
    }
}
