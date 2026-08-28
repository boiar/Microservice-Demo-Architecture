<?php

namespace App\Contracts\Services;

interface IRabbitMQService
{
    /**
     * Publish a message to RabbitMQ.
     *
     * @param string $exchange
     * @param string $routingKey
     * @param array $data
     * @return void
     */
    public function publish(string $exchange, string $routingKey, array $data): void;
}
