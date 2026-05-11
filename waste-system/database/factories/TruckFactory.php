<?php

namespace Database\Factories;

use App\Models\Truck;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Truck>
 */
class TruckFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $lat = fake()->latitude(28.4, 28.9);
        $lng = fake()->longitude(76.8, 77.4);

        return [
            'registration_number' => 'DL'.fake()->unique()->bothify('##??####'),
            'capacity_kg' => fake()->numberBetween(500, 2000),
            'waste_types_supported' => fake()->randomElements(['dry', 'wet', 'hazardous', 'mixed'], fake()->numberBetween(2, 4)),
            'driver_id' => null,
            'status' => fake()->randomElement(['available', 'on_route', 'maintenance']),
            'current_location' => ['type' => 'Point', 'coordinates' => [$lng, $lat]],
        ];
    }
}
