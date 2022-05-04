<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport;

use PhpQueues\Transport\Message;

final class AmqpMessage implements Message
{
    /**
     * @psalm-readonly
     *
     * @psalm-var non-empty-string
     */
    public string $id;

    /**
     * @psalm-readonly
     */
    public string $payload;

    /**
     * @psalm-readonly
     */
    public array $headers;

    /**
     * @psalm-readonly
     */
    public AmqpDestination $destination;

    /**
     * @psalm-readonly
     */
    public bool $persist;

    /**
     * @psalm-readonly
     */
    public bool $mandatory;

    /**
     * @psalm-readonly
     */
    public bool $immediate;

    /**
     * @psalm-param non-empty-string $id
     */
    public function __construct(
        string $id,
        string $payload,
        AmqpDestination $destination,
        array $headers = [],
        bool $persist = false,
        bool $mandatory = false,
        bool $immediate = false
    ) {
        $this->id = $id;
        $this->payload = $payload;
        $this->headers = $headers;
        $this->destination = $destination;
        $this->persist = $persist;
        $this->mandatory = $mandatory;
        $this->immediate = $immediate;
    }

    public function withHeaders(array $headers): AmqpMessage
    {
        $message = clone $this;
        $message->headers = \array_merge($message->headers, $headers);

        return $message;
    }

    public function changeDestination(AmqpDestination $destination): AmqpMessage
    {
        $message = clone $this;
        $message->destination = $destination;

        return $message;
    }
}
