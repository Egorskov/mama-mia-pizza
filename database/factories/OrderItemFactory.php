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
        $good = Good::with('options')->inRandomOrder()->first();
        $option = $good->options->isNotEmpty() ? $good->options->random() : null;

        $basePrice = $good->price;
        $optionPrice = $option ? $option->price : 0;
        $quantity = fake()->numberBetween(1, 10);

        return [
            'order_id' => Order::factory(),
            'good_id' => $good->id,
            'good_option_id' => $option?->id,
            'quantity' => $quantity,
            'base_price' => $basePrice,
            'option_price' => $optionPrice,
            'total_price' => ($basePrice + $optionPrice) * $quantity
        ];
    }
}
