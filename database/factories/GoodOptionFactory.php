<?php

namespace Database\Factories;

use App\Models\Good;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GoodOption>
 */
class GoodOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'good_id' => Good::factory(),
            'type' => fake()->randomElement(['sause', 'stuffing']),
            'name' => $this->faker->name(),
            'price' => $this->faker->randomFloat(2, 10, 100),
        ];
    }
}
