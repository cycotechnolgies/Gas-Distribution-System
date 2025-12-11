<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCylinder extends Model
{
    use HasFactory;

    protected $table = 'customer_cylinders';

    protected $fillable = [
        'customer_id',
        'gas_type_id',
        'transaction_type',
        'quantity',
        'transaction_date',
        'reference',
        'notes'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'quantity' => 'integer'
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

    public function scopeIssued($query)
    {
        return $query->where('transaction_type', 'Issued');
    }

    public function scopeReturned($query)
    {
        return $query->where('transaction_type', 'Returned');
    }

    public function scopeForGasType($query, $gasTypeId)
    {
        return $query->where('gas_type_id', $gasTypeId);
    }

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeRecentFirst($query)
    {
        return $query->orderBy('transaction_date', 'desc');
    }

    /**
     * Get current balance for a specific gas type for a customer
     *
     * @param int $customerId
     * @param int $gasTypeId
     * @return int
     */
    public static function getCurrentBalance($customerId, $gasTypeId)
    {
        $issued = self::forCustomer($customerId)
            ->forGasType($gasTypeId)
            ->issued()
            ->sum('quantity');

        $returned = self::forCustomer($customerId)
            ->forGasType($gasTypeId)
            ->returned()
            ->sum('quantity');

        return $issued - $returned;
    }

    /**
     * Get transaction summary for a customer
     *
     * @param int $customerId
     * @return array
     */
    public static function getTransactionSummary($customerId)
    {
        $customer = Customer::find($customerId);
        $gasTypes = GasType::all();

        $summary = [];
        foreach ($gasTypes as $gasType) {
            $issued = self::forCustomer($customerId)
                ->forGasType($gasType->id)
                ->issued()
                ->sum('quantity');

            $returned = self::forCustomer($customerId)
                ->forGasType($gasType->id)
                ->returned()
                ->sum('quantity');

            $summary[$gasType->id] = [
                'gas_type' => $gasType->name,
                'issued' => $issued,
                'returned' => $returned,
                'balance' => $issued - $returned
            ];
        }

        return $summary;
    }

    /**
     * Check if customer owes cylinders for a gas type
     *
     * @param int $customerId
     * @param int $gasTypeId
     * @return bool
     */
    public static function owesUnreturnedCylinders($customerId, $gasTypeId)
    {
        return self::getCurrentBalance($customerId, $gasTypeId) > 0;
    }

    /**
     * Get cylinders awaiting return
     *
     * @param int $customerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPendingReturns($customerId)
    {
        $gasTypes = GasType::all();
        $pending = [];

        foreach ($gasTypes as $gasType) {
            $balance = self::getCurrentBalance($customerId, $gasType->id);
            if ($balance > 0) {
                $pending[$gasType->id] = [
                    'gas_type' => $gasType->name,
                    'pending_return_count' => $balance
                ];
            }
        }

        return $pending;
    }
}
