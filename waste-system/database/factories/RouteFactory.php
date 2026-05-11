<?php

namespace Database\Factories;

use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Route>
 */
class RouteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'truck_id' => null,
            'admin_id' => null,
            'name' => 'Route '.fake()->city(),
            'description' => fake()->sentence(8),
            'scheduled_date' => fake()->dateTimeBetween('-2 days', '+5 days'),
            'status' => fake()->randomElement(['planned', 'active', 'completed']),
            'estimated_duration_minutes' => fake()->numberBetween(60, 240),
            'stops' => [],
        ];
    }
}
