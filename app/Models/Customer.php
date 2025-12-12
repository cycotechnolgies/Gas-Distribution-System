<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'nic',
        'city',
        'customer_type',
        'credit_limit',
        'outstanding_balance',
        'full_cylinders_issued',
        'empty_cylinders_returned',
        'status'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'full_cylinders_issued' => 'integer',
        'empty_cylinders_returned' => 'integer'
    ];

    /**
     * Relationships
     */

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function customPrices()
    {
        return $this->hasMany(GasTypeCustomerPrice::class);
    }

    public function cylinderTransactions()
    {
        return $this->hasMany(CustomerCylinder::class);
    }

    public function gasTypeCustomerPrices()
    {
        return $this->belongsToMany(GasType::class, 'gas_type_customer_prices')
            ->withPivot('custom_price', 'notes')
            ->withTimestamps();
    }

    /**
     * Pricing Methods
     */

    /**
     * Get price for a specific gas type
     * First checks custom price override, then falls back to category tier pricing
     *
     * @param int $gasTypeId
     * @return float|null
     */
    public function getPriceForGasType($gasTypeId)
    {
        // Check for custom price override
        $customPrice = $this->customPrices()
            ->where('gas_type_id', $gasTypeId)
            ->first();

        if ($customPrice) {
            return $customPrice->custom_price;
        }

        // Fall back to category-based tier pricing
        return $this->getTypeBasedPrice($gasTypeId);
    }

    /**
     * Get category tier pricing for a gas type
     *
     * @param int $gasTypeId
     * @return float|null
     */
    public function getTypeBasedPrice($gasTypeId)
    {
        $tierPrice = CustomerPricingTier::where('customer_type', $this->customer_type)
            ->where('gas_type_id', $gasTypeId)
            ->first();

        return $tierPrice ? $tierPrice->price : null;
    }

    /**
     * Credit Management Methods
     */

    /**
     * Get current outstanding balance
     *
     * @return float
     */
    public function getOutstandingBalance()
    {
        return $this->outstanding_balance ?? 0;
    }

    /**
     * Get available credit
     *
     * @return float
     */
    public function getCreditAvailable()
    {
        $creditLimit = $this->credit_limit ?? 0;
        $outstanding = $this->getOutstandingBalance();
        return $creditLimit - $outstanding;
    }

    /**
     * Check if customer has exceeded credit limit
     *
     * @return bool
     */
    public function isOverCredit()
    {
        return $this->getCreditAvailable() < 0;
    }

    /**
     * Update outstanding balance
     *
     * @param float $amount
     * @return void
     */
    public function updateBalance($amount)
    {
        $this->outstanding_balance = max(0, ($this->outstanding_balance ?? 0) + $amount);
        $this->save();
    }

    /**
     * Cylinder Tracking Methods
     */

    /**
     * Get net full cylinders (issued - returned)
     *
     * @return int
     */
    public function getFullCylindersNet()
    {
        $issued = $this->full_cylinders_issued ?? 0;
        $returned = $this->empty_cylinders_returned ?? 0;
        return $issued - $returned;
    }

    /**
     * Get cylinder balance for specific gas type
     *
     * @param int $gasTypeId
     * @return int
     */
    public function getCylinderBalance($gasTypeId)
    {
        $issued = $this->cylinderTransactions()
            ->where('gas_type_id', $gasTypeId)
            ->where('transaction_type', 'Issued')
            ->sum('quantity');

        $returned = $this->cylinderTransactions()
            ->where('gas_type_id', $gasTypeId)
            ->where('transaction_type', 'Returned')
            ->sum('quantity');

        return $issued - $returned;
    }

    /**
     * Get cylinder balances for all gas types
     *
     * @return array
     */
    public function getAllCylinderBalances()
    {
        $gasTypes = GasType::all();
        $balances = [];

        foreach ($gasTypes as $gasType) {
            $balances[$gasType->id] = [
                'name' => $gasType->name,
                'balance' => $this->getCylinderBalance($gasType->id)
            ];
        }

        return $balances;
    }

    /**
     * Record cylinder transaction (Issued or Returned)
     *
     * @param int $gasTypeId
     * @param string $transactionType (Issued|Returned)
     * @param int $quantity
     * @param string|null $reference
     * @param string|null $notes
     * @return CustomerCylinder
     */
    public function recordCylinderTransaction($gasTypeId, $transactionType, $quantity, $reference = null, $notes = null)
    {
        // Update cumulative counts
        if ($transactionType === 'Issued') {
            $this->full_cylinders_issued = ($this->full_cylinders_issued ?? 0) + $quantity;
        } else {
            $this->empty_cylinders_returned = ($this->empty_cylinders_returned ?? 0) + $quantity;
        }
        $this->save();

        // Create transaction record
        return $this->cylinderTransactions()->create([
            'gas_type_id' => $gasTypeId,
            'transaction_type' => $transactionType,
            'quantity' => $quantity,
            'transaction_date' => now(),
            'reference' => $reference,
            'notes' => $notes
        ]);
    }

    /**
     * Get recent cylinder transactions
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentCylinderTransactions($limit = 10)
    {
        return $this->cylinderTransactions()
            ->orderBy('transaction_date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Check if customer can be deleted
     *
     * @return bool
     */
    public function canBeDeleted()
    {
        // Cannot delete if has outstanding balance
        if ($this->getOutstandingBalance() > 0) {
            return false;
        }

        // Cannot delete if has unclosed orders
        if ($this->orders()->where('status', '!=', 'Completed')->exists()) {
            return false;
        }

        return true;
    }
}

