<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('price.request', false, true, false, false);
$channel->queue_declare('price.response', false, true, false, false);

$supplierName = getenv('SUPPLIER_NAME') ?: 'Supplier-' . rand(100, 999);

echo "[$supplierName] Listening...\n";

$callback = function ($msg) use ($supplierName, $channel) {
    $data = json_decode($msg->body, true);

    // эмуляция сетевой задержки
    usleep(rand(200, 2500) * 1000);

    $response = [
        'pizza_id' => $data['pizza_id'],
        'supplier' => $supplierName,
        'price' => rand(500, 900)
    ];

    $msgOut = new AMQPMessage(json_encode($response));
    $channel->basic_publish($msgOut, '', 'price.response');

    $msg->ack();
    echo "✅ {$supplierName} sent price {$response['price']} for pizza {$response['pizza_id']}\n";
};

$channel->basic_consume('price.request', '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}
