<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ConsumeSupplierPrices extends Command
{
    protected $signature = 'suppliers:consume';
    protected $description = 'Consume supplier price responses and broadcast via SSE';

    public function handle()
    {
        $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('price.response', false, true, false, false);
        $channel->queue_declare('price.response', false, true, false, false);
        $channel->queue_bind('price.response', 'price_exchange', 'price.response');

        $callback = function ($msg) {
            $data = json_decode($msg->body, true);
            $key = "pizza:{$data['pizza_id']}:offers";
            $offers = Cache::get($key, []);
            $offers[$data['supplier']] = $data['price'];
            Cache::put($key, $offers, now()->addMinutes(5));

            // Пушим в Redis канал для SSE
            Redis::publish('pizza.prices', json_encode($data));

            $msg->ack();
            echo "📩 Received from {$data['supplier']}: {$data['price']}\n";
        };

        $channel->basic_consume('price.response', '', false, false, false, false, $callback);
        while ($channel->is_consuming()) $channel->wait();
    }
}
