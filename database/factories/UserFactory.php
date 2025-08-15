<?php

namespace Database\Factories;

use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'phone_number' => '+7' . fake()->numerify('##########'),
            'birthday' => fake()->date(),
            'admin' => fake()->randomElement(['yes', 'no']),
        ];
    }

    public function admin()
    {
        return $this->state([
            'admin' => 'yes',
        ]);
    }
//     public function configure()
//     {
//         return $this->afterCreating(function ($user) {
//            $addressCount = rand(1, 3);
//            UserAddress::factory()
//                ->count($addressCount)
//                ->for($user)
//                ->create();
//            if ($addressCount > 1) {
//                $user->addresses()->first()->update(['is_default'=>'true'])
//            }
//         });
//     }

}
