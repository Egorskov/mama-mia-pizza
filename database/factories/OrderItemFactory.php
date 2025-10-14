<?php

namespace Database\Factories;

use App\Models\Good;
use App\Models\Order;
use App\Models\GoodOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $good = Good::query()->inRandomOrder()->first();
        $option = $good->options()->inRandomOrder()->first();
        return [
            'order_id' => Order::factory(),
            'good_id' => $good->id,
            'quantity' => fake()->numberBetween(1, 10),
            'good_option_id' => $option ? $option->id : null,
            'base_price' => $good->price,
            'total_price' => function (array $price) {
                return ($price['base_price'] + $price['option_price'])
                    * $price['quantity'];
            }
        ];
    }
}
