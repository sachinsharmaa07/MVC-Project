<?php

namespace Database\Factories;

use App\Models\PickupRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PickupRequest>
 */
class PickupRequestFactory extends Factory
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
            'citizen_id' => null,
            'location' => ['type' => 'Point', 'coordinates' => [$lng, $lat]],
            'address' => fake()->streetAddress().', Delhi',
            'waste_type' => fake()->randomElement(['dry', 'wet', 'hazardous', 'mixed']),
            'segregation_status' => fake()->randomElement(['compliant', 'non_compliant', 'pending_review']),
            'status' => fake()->randomElement(['pending', 'assigned', 'in_progress', 'completed', 'cancelled']),
            'scheduled_at' => fake()->optional(0.4)->dateTimeBetween('now', '+3 days'),
            'photo_path' => null,
            'notes' => fake()->optional()->sentence(10),
        ];
    }
}
