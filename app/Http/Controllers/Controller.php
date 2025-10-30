<?php

namespace App\Http\Controllers;

use App\Models\Good;
use App\Models\GoodOption;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function extracted(mixed $validated, Order $order)
    {
        $totalAmount = 0;
        foreach ($validated['items'] as $item) {
            $good = Good::findOrFail($item['good_id']);
            $basePrice = $good->price;
            $quantity = $item['quantity'];
            $goodOptionId = null;
            $optionPrice = 0;
            $totalPrice = $basePrice * $quantity;

            if (!empty($item['good_option_id'])) {
                $goodOption = GoodOption::findOrFail($item['good_option_id']);
                $goodOptionId = $goodOption->id;
                $optionPrice = $goodOption->price;
                $totalPrice = ($basePrice + $optionPrice) * $item['quantity'];
            }

            OrderItem::create([
                'order_id' => $order->id,
                'good_id' => $good->id,
                'quantity' => $quantity,
                'good_option_id' => $goodOptionId,
                'base_price' => $basePrice,
                'option_price' => $optionPrice,
                'total_price' => $totalPrice,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $totalAmount += $totalPrice;
        }
    }

}
