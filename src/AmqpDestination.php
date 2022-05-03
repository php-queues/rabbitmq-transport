<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport;

use PhpQueues\Transport\Destination;

final class AmqpDestination implements Destination
{
    /**
     * @psalm-readonly
     */
    public string $exchange;

    /**
     * @psalm-readonly
     */
    public string $routingKey;

    public function __construct(string $exchange, string $routingKey)
    {
        $this->exchange = $exchange;
        $this->routingKey = $routingKey;
    }
}
