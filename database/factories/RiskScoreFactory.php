<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RiskScore>
 */
class RiskScoreFactory extends Factory
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
            'weather_score' => $this->faker->randomFloat(1, 0, 100),
            'inflation_score' => $this->faker->randomFloat(1, 0, 100),
            'currency_score' => $this->faker->randomFloat(1, 0, 100),
            'sentiment_score' => $this->faker->randomFloat(1, 0, 100),
            'total_score' => $this->faker->randomFloat(1, 0, 100),
            'calculated_at' => now(),
        ];
    }
}
