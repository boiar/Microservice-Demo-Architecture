<?php

namespace App\Services;

use App\Contracts\Services\IRabbitMQService;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Log;
use Exception;

class RabbitMQService implements IRabbitMQService
{
    public function publish(string $exchange, string $routingKey, array $data): void
    {
        try {
            // It is generally better to inject config rather than using env() directly in services,
            // but we'll use env() here to maintain consistency with the original code.
            // Consider moving this to config/queue.php or config/services.php.
            $connection = new AMQPStreamConnection(
                env('RABBITMQ_HOST', 'rabbitmq'),
                env('RABBITMQ_PORT', 5672),
                env('RABBITMQ_USER', 'admin'),
                env('RABBITMQ_PASSWORD', 'admin')
            );
            
            $channel = $connection->channel();
            $msg = new AMQPMessage(json_encode($data));
            
            $channel->basic_publish($msg, $exchange, $routingKey);
            Log::info("[RabbitMQService] RabbitMQ publish successful to {$exchange}/{$routingKey}");
            
            $channel->close();
            $connection->close();
        } catch (Exception $e) {
            Log::error('[RabbitMQService] RabbitMQ publish failed: ' . $e->getMessage());
        }
    }
}
