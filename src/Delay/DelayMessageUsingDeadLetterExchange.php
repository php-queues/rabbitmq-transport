<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport\Delay;

use PhpQueues\RabbitmqTransport\AmqpDestination;
use PhpQueues\RabbitmqTransport\Queue;
use PhpQueues\RabbitmqTransport\TransportConfigurator;
use PhpQueues\Transport\Delay\DelayMessage;
use PhpQueues\RabbitmqTransport\AmqpMessage;
use PhpQueues\Transport\Delay\DelaysAreNotSupported;
use PhpQueues\Transport\Destination;
use PhpQueues\Transport\Message;
use PhpQueues\Transport\Producer;
use PhpQueues\RabbitmqTransport\AmqpProducer;

/**
 * @psalm-template M as AmqpMessage
 * @psalm-template D as AmqpDelayDestination
 * @psalm-template P as AmqpProducer
 *
 * @template-implements DelayMessage<M, D, P>
 */
final class DelayMessageUsingDeadLetterExchange implements DelayMessage
{
    private TransportConfigurator $transportConfigurator;

    /**
     * @psalm-var callable(positive-int):positive-int
     */
    private $expiresFormula;

    /**
     * @psalm-param (callable(positive-int):positive-int)|null $expiresFormula
     */
    public function __construct(TransportConfigurator $transportConfigurator, ?callable $expiresFormula = null)
    {
        /** @var callable(positive-int):positive-int */
        $expiresFormula ??= fn (int $delay): int => $delay * 2;

        $this->transportConfigurator = $transportConfigurator;
        $this->expiresFormula = $expiresFormula;
    }

    /**
     * @psalm-param P $producer
     * @psalm-param M $message
     * @psalm-param D $destination
     * @psalm-param positive-int $delay
     *
     * @throws DelaysAreNotSupported
     */
    public function delay(Producer $producer, Message $message, Destination $destination, int $delay): void
    {
        $this->transportConfigurator->bindQueue(Queue::default($destination->queue)->makeDurable()->withArguments([
            'x-message-ttl' => $delay,
            'x-expires' => ($this->expiresFormula)($delay),
            'x-dead-letter-exchange' => $destination->exchange,
            'x-dead-letter-routing-key' => $message->destination->routingKey,
        ]));

        $producer->publish($message->changeDestination(new AmqpDestination('', $destination->queue)));
    }
}
