<?php
namespace Database\Factories;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'license_number' => $this->faker->unique()->bothify('DL####'),
            // Add other required fields for Driver
        ];
    }
}
