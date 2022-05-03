<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport;

interface AmqpOperator
{
    /**
     * Declares the queue.
     */
    public function declareQueue(Queue $queue): void;

    /**
     * Declares the exchange.
     */
    public function declareExchange(Exchange $exchange): void;

    /**
     * Bind the queue to given exchange using routing key and arguments.
     */
    public function bindQueue(Queue $queue, Exchange $exchange, string $routingKey, array $arguments = []): void;

    /**
     * Bind the source exchange to destination exchange using routing key, argument and no wait flag.
     */
    public function bindExchange(Exchange $source, Exchange $destination, string $routingKey, bool $noWait = false, array $arguments = []): void;
}
