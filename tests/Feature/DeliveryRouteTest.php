<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Driver;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_delivery_route()
    {
        $user = User::factory()->create();
        $driver = Driver::factory()->create();
        $assistant = \App\Models\Assistant::factory()->create();
        $vehicle = \App\Models\Vehicle::factory()->create();
        $customer = Customer::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('delivery-routes.store'), [
            'route_name' => 'Test Route',
            'route_date' => now()->format('Y-m-d'),
            'driver_id' => $driver->id,
            'assistant_id' => $assistant->id,
            'vehicle_id' => $vehicle->id,
            'notes' => 'Test notes',
            'stops' => [
                [
                    'customer_id' => $customer->id,
                    'order_id' => null,
                    'planned_time' => '10:00',
                ]
            ],
        ]);

        // Debug output for validation errors
        if ($response->status() === 302) {
            $response = $this->followRedirects($response);
            fwrite(STDOUT, $response->getContent());
        }

        $response->assertRedirect();
        $this->assertDatabaseHas('delivery_routes', [
            'route_name' => 'Test Route',
            'driver_id' => $driver->id,
        ]);
    }
}
