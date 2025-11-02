<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;


class SendOrderConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle(): void
    {
        // Имитация отправки email
        \Log::info("Sending order confirmation for order #{$this->order->id}");

        // В реальном проекте здесь было бы:
        // Mail::to($this->order->user->email)->send(new OrderConfirmationMail($this->order));

        // Имитируем задержку отправки (3 секунды)
        sleep(3);

        \Log::info("Order confirmation sent for order #{$this->order->id}");
    }
}
