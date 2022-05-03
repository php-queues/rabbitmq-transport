<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport;

final class Queue
{
    /**
     * @psalm-var non-empty-string
     *
     * @psalm-readonly
     */
    public string $name;

    /**
     * @psalm-readonly
     */
    public bool $passive = false;

    /**
     * @psalm-readonly
     */
    public bool $durable = false;

    /**
     * @psalm-readonly
     */
    public bool $exclusive = false;

    /**
     * @psalm-readonly
     */
    public bool $autoDelete = false;

    /**
     * @psalm-readonly
     */
    public bool $noWait = false;

    /**
     * @psalm-readonly
     */
    public array $arguments = [];

    private function __construct(string $name)
    {
        if ('' === $name) {
            throw new \InvalidArgumentException('Queue name cannot be an empty string.');
        }

        $this->name = $name;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function default(string $queue): Queue
    {
        return new self($queue);
    }

    public function makeDurable(): Queue
    {
        $queue = clone $this;
        $queue->durable = true;

        return $queue;
    }

    public function makeExclusive(): Queue
    {
        $queue = clone $this;
        $queue->exclusive = true;

        return $queue;
    }

    public function makePassive(): Queue
    {
        $queue = clone $this;
        $queue->passive = true;

        return $queue;
    }

    public function makeAutoDelete(): Queue
    {
        $queue = clone $this;
        $queue->autoDelete = true;

        return $queue;
    }

    public function noWait(): Queue
    {
        $queue = clone $this;
        $queue->noWait = true;

        return $queue;
    }

    public function withArguments(array $arguments): Queue
    {
        $queue = clone $this;
        $queue->arguments = \array_merge($queue->arguments, $arguments);

        return $queue;
    }
}
