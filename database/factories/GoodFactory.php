<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Good>
 */
class GoodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => fake()->text(25),
            'price' => fake()->randomFloat(2, 10, 100),
            'weight' => fake()->numberBetween(100, 1000),
            'category' => fake()->randomElement(['pizza', 'drink']),
        ];
    }
}
