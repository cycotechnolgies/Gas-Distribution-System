<?php
namespace Database\Factories;

use App\Models\Assistant;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssistantFactory extends Factory
{
    protected $model = Assistant::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'status' => 'active', // must match enum
            'total_deliveries' => 0,
            'average_rating' => 5.0,
            'address' => $this->faker->address(),
            'hire_date' => $this->faker->date(),
            'notes' => $this->faker->sentence(),
        ];
    }
}
