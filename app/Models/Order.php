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
        'status',
        'is_urgent',
        'order_total',
        'notes',
        'order_date',
        'loaded_at',
        'delivered_at',
        'completed_at'
    ];

    protected $casts = [
        'is_urgent' => 'boolean',
        'order_date' => 'date',
        'loaded_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
        'order_total' => 'decimal:2'
    ];

    /**
     * Relationships
     */

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function deliveryRoute()
    {
        return $this->belongsTo(DeliveryRoute::class)->withDefault();
    }

    /**
     * Status Management Methods
     */

    /**
     * Check if order can transition to a specific status
     *
     * @param string $newStatus
     * @return bool
     */
    public function canTransitionTo($newStatus)
    {
        $validTransitions = [
            'Pending' => ['Loaded', 'Cancelled'],
            'Loaded' => ['Delivered', 'Cancelled'],
            'Delivered' => ['Completed'],
            'Completed' => [],
            'Cancelled' => []
        ];

        return in_array($newStatus, $validTransitions[$this->status] ?? []);
    }

    /**
     * Update order status with timestamp tracking
     *
     * @param string $newStatus
     * @return bool
     */
    public function updateOrderStatus($newStatus)
    {
        if (!$this->canTransitionTo($newStatus)) {
            return false;
        }

        $this->status = $newStatus;

        // Update timestamps based on status
        match($newStatus) {
            'Loaded' => $this->loaded_at = now(),
            'Delivered' => $this->delivered_at = now(),
            'Completed' => $this->completed_at = now(),
            default => null
        };

        return $this->save();
    }

    /**
     * Check if order is pending
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'Pending';
    }

    /**
     * Check if order is in delivery
     *
     * @return bool
     */
    public function isInDelivery()
    {
        return in_array($this->status, ['Loaded', 'Delivered']);
    }

    /**
     * Check if order is completed
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === 'Completed';
    }

    /**
     * Check if order is cancelled
     *
     * @return bool
     */
    public function isCancelled()
    {
        return $this->status === 'Cancelled';
    }

    /**
     * Urgent Order Methods
     */

    /**
     * Mark order as urgent
     *
     * @return bool
     */
    public function markUrgent()
    {
        return $this->update(['is_urgent' => true]);
    }

    /**
     * Mark order as non-urgent
     *
     * @return bool
     */
    public function unmarkUrgent()
    {
        return $this->update(['is_urgent' => false]);
    }

    /**
     * Order Calculation Methods
     */

    /**
     * Calculate order total from items
     *
     * @return float
     */
    public function calculateTotal()
    {
        return $this->items()->sum('line_total');
    }

    /**
     * Recalculate and update order total
     *
     * @return bool
     */
    public function updateTotal()
    {
        $this->order_total = $this->calculateTotal();
        return $this->save();
    }

    /**
     * Get order total with optional recalculation
     *
     * @return float
     */
    public function getOrderTotal()
    {
        return $this->order_total ?? $this->calculateTotal();
    }

    /**
     * Get total quantity of cylinders in order
     *
     * @return int
     */
    public function getTotalQuantity()
    {
        return $this->items()->sum('quantity');
    }

    /**
     * Delivery Route Methods
     */

    /**
     * Assign order to delivery route
     *
     * @param int $routeId
     * @return bool
     */
    public function assignToRoute($routeId)
    {
        return $this->update(['delivery_route_id' => $routeId]);
    }

    /**
     * Check if order is assigned to a route
     *
     * @return bool
     */
    public function hasRoute()
    {
        return !is_null($this->delivery_route_id);
    }

    /**
     * Order Item Methods
     */

    /**
     * Add item to order
     *
     * @param int $gasTypeId
     * @param int $quantity
     * @param float $unitPrice
     * @param string|null $notes
     * @return OrderItem
     */
    public function addItem($gasTypeId, $quantity, $unitPrice, $notes = null)
    {
        $lineTotal = $quantity * $unitPrice;

        return $this->items()->create([
            'gas_type_id' => $gasTypeId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'line_total' => $lineTotal,
            'notes' => $notes
        ]);
    }

    /**
     * Remove item from order
     *
     * @param int $itemId
     * @return bool
     */
    public function removeItem($itemId)
    {
        $item = $this->items()->find($itemId);
        if ($item) {
            $item->delete();
            $this->updateTotal();
            return true;
        }
        return false;
    }

    /**
     * Check if order can be deleted
     *
     * @return bool
     */
    public function canBeDeleted()
    {
        return in_array($this->status, ['Pending', 'Cancelled']);
    }

    /**
     * Get item count
     *
     * @return int
     */
    public function getItemCount()
    {
        return $this->items()->count();
    }

    /**
     * Check if order has items
     *
     * @return bool
     */
    public function hasItems()
    {
        return $this->getItemCount() > 0;
    }
}
