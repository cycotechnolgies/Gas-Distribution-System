<?php
namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition()
    {
        return [
            'vehicle_number' => $this->faker->unique()->bothify('VN####'),
            'model' => $this->faker->word(),
            'type' => 'Truck',
            'capacity' => 1000,
            'status' => 'active', // must match enum
            'total_deliveries' => 0,
            'total_km' => 0,
            'fuel_consumption' => 10.5,
            'last_maintenance_date' => $this->faker->date(),
            'next_maintenance_due' => $this->faker->date(),
            'registration_expiry' => $this->faker->date(),
            'purchase_date' => $this->faker->date(),
            'notes' => $this->faker->sentence(),
        ];
    }
}
