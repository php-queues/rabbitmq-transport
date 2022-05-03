<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport;

final class QueueBinding
{
    /**
     * @psalm-readonly
     */
    public Exchange $exchange;

    /**
     * @psalm-readonly
     */
    public string $routingKey;

    /**
     * @psalm-readonly
     */
    public array $arguments;

    /**
     * @psalm-readonly
     */
    public bool $noWait;

    public function __construct(Exchange $exchange, string $routingKey, bool $noWait = false, array $arguments = [])
    {
        $this->exchange = $exchange;
        $this->routingKey = $routingKey;
        $this->arguments = $arguments;
        $this->noWait = $noWait;
    }
}
