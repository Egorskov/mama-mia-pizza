<?php

namespace App\Http\Controllers;

use App\Jobs\SendOrderConfirmation;
use App\Models\Order;
use Illuminate\Http\Request;

class TestQueueController extends Controller
{
    public function testQueue()
    {
        // Берем последний заказ или создаем фиктивный
        $order = Order::latest()->first();

        if (!$order) {
            return response()->json(['message' => 'No orders found']);
        }

        // Отправляем задачу в очередь
        SendOrderConfirmation::dispatch($order);

        return response()->json([
            'message' => 'Order confirmation job queued!',
            'order_id' => $order->id,
            'queued_at' => now()->toDateTimeString()
        ]);
    }

    public function testSync()
    {
        $order = Order::latest()->first();

        if (!$order) {
            return response()->_json(['message' => 'No orders found']);
        }

        // Выполняем синхронно (без очереди)
        \Log::info("Sending order confirmation SYNC for order #{$order->id}");
        sleep(3); // Имитация задержки
        \Log::info("Order confirmation sent SYNC for order #{$order->id}");

        return response()->json([
            'message' => 'Order confirmation sent synchronously!',
            'order_id' => $order->id,
            'sent_at' => now()->toDateTimeString()
        ]);
    }
}
