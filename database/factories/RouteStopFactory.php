<?php
namespace Database\Factories;

use App\Models\RouteStop;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class RouteStopFactory extends Factory
{
    protected $model = RouteStop::class;

    public function definition()
    {
        return [
            'customer_id' => Customer::factory(),
            'stop_order' => 1,
            'planned_time' => now()->addHour(),
            // Add other required fields if needed
        ];
    }
}
