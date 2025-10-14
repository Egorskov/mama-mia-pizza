<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAddress>
 */
class UserAddressFactory extends Factory
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
            'street' => fake()->streetName(),
            'number' => fake()->buildingNumber(),
            'appartment' => fake()->numberBetween(1, 100),
        ];
    }

    public function configure(): Factory|UserAddressFactory
    {
        return $this->afterCreating(function (User $user) {
            UserAddress::factory()->count(rand(1,3))->create(['user_id'=>$user->id]);
        });
    }
}
