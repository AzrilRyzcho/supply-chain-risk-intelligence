<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
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
            'title' => $this->faker->sentence,
            'source' => $this->faker->company,
            'url' => $this->faker->url,
            'sentiment' => $this->faker->randomElement(['positive', 'neutral', 'negative']),
            'positive_score' => $this->faker->numberBetween(0, 5),
            'negative_score' => $this->faker->numberBetween(0, 5),
            'published_at' => now(),
        ];
    }
}
