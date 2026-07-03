<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Weather>
 */
class WeatherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'country_id' => Country::factory(),
            'temperature' => $this->faker->randomFloat(1, -10, 40),
            'rain' => $this->faker->randomFloat(1, 0, 100),
            'wind_speed' => $this->faker->randomFloat(1, 0, 50),
            'storm_risk' => $this->faker->randomFloat(1, 0, 100),
            'fetched_at' => now(),
        ];
    }
}
