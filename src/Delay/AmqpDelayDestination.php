<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport\Delay;

use PhpQueues\Transport\Destination;

final class AmqpDelayDestination implements Destination
{
    /**
     * @psalm-readonly
     *
     * @psalm-var non-empty-string
     */
    public string $queue;

    /**
     * @psalm-readonly
     */
    public string $exchange;

    /**
     * @psalm-readonly
     */
    public string $routingKey;

    /**
     * @psalm-readonly
     */
    public ?string $delayedQueueFormula;

    public function __construct(string $queue, string $routingKey, string $exchange = '', ?string $delayedQueueFormula = null)
    {
        if ('' === $queue) {
            throw new \InvalidArgumentException('Queue cannot be an empty string.');
        }

        $this->queue = $queue;
        $this->exchange = $exchange;
        $this->routingKey = $routingKey;
        $this->delayedQueueFormula = $delayedQueueFormula;
    }
}
