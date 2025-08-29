<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'user_address_id' => UserAddress::all()->random()->id,
            'delivery_time' => fake()->dateTimeBetween('now', '+1 hour'),
            'delivery_status' => fake()->randomElement([
                'pending', 'delivered',
                'processing', 'shipping', 'cancelled'
            ])
        ];
    }
}
