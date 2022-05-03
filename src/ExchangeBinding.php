<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport;

final class ExchangeBinding
{
    /**
     * @psalm-readonly
     */
    public Exchange $destination;

    /**
     * @psalm-readonly
     */
    public string $routingKey;

    /**
     * @psalm-readonly
     */
    public bool $noWait;

    /**
     * @psalm-readonly
     */
    public array $arguments;

    public function __construct(Exchange $destination, string $routingKey, bool $noWait = false, array $arguments = [])
    {
        $this->destination = $destination;
        $this->routingKey = $routingKey;
        $this->noWait = $noWait;
        $this->arguments = $arguments;
    }
}
