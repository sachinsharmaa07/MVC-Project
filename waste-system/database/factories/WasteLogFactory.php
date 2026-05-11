<?php

namespace Database\Factories;

use App\Models\WasteLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WasteLog>
 */
class WasteLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pickup_request_id' => null,
            'collected_weight_kg' => fake()->randomFloat(2, 5, 120),
            'segregation_compliant' => fake()->boolean(80),
            'collected_at' => fake()->dateTimeBetween('-7 days', 'now'),
            'driver_notes' => fake()->optional()->sentence(8),
        ];
    }
}
