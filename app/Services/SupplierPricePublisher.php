<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class SupplierPricePublisher
{
    /**
     * @throws \Exception
     */
    public function sendPriceRequests(string $pizzaId, array $suppliers): void
    {
        $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->exchange_declare('price_exchange', 'direct', false, true, false);
        $channel->queue_declare('price.request', false, true, false, false);
        $channel->queue_bind('price.request', 'price_exchange', 'price.request');

        foreach ($suppliers as $supplier) {
            $msg = new AMQPMessage(json_encode([
                'pizza_id' => $pizzaId,
                'supplier' => $supplier,
            ]));
            $channel->basic_publish($msg, 'price_exchange', 'price.request');
        }

        $channel->close();
        $connection->close();
    }
}
