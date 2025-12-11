<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'gas_type_id',
        'quantity',
        'unit_price',
        'line_total',
        'notes',
        'delivered_adjusted'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2'
    ];

    /**
     * Relationships
     */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function gasType()
    {
        return $this->belongsTo(GasType::class);
    }

    /**
     * Calculation Methods
     */

    /**
     * Calculate line total
     *
     * @return float
     */
    public function calculateLineTotal()
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Update line total
     *
     * @return bool
     */
    public function updateLineTotal()
    {
        $this->line_total = $this->calculateLineTotal();
        return $this->save();
    }

    /**
     * Update quantity and recalculate
     *
     * @param int $newQuantity
     * @return bool
     */
    public function updateQuantity($newQuantity)
    {
        $this->quantity = $newQuantity;
        return $this->updateLineTotal();
    }

    /**
     * Update unit price and recalculate
     *
     * @param float $newPrice
     * @return bool
     */
    public function updatePrice($newPrice)
    {
        $this->unit_price = $newPrice;
        return $this->updateLineTotal();
    }

    /**
     * Get price information
     *
     * @return array
     */
    public function getPriceInfo()
    {
        return [
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'line_total' => $this->line_total ?? $this->calculateLineTotal()
        ];
    }

    /**
     * Validation Methods
     */

    /**
     * Check if quantity is positive
     *
     * @return bool
     */
    public function hasValidQuantity()
    {
        return $this->quantity > 0;
    }

    /**
     * Check if price is valid
     *
     * @return bool
     */
    public function hasValidPrice()
    {
        return $this->unit_price >= 0;
    }

    /**
     * Check if item is valid for processing
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->hasValidQuantity() && $this->hasValidPrice() && $this->gasType !== null;
    }
}
