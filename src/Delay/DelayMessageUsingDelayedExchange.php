<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport\Delay;

use PhpQueues\RabbitmqTransport\AmqpDestination;
use PhpQueues\RabbitmqTransport\Exchange;
use PhpQueues\RabbitmqTransport\ExchangeBinding;
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
final class DelayMessageUsingDelayedExchange implements DelayMessage
{
    private TransportConfigurator $transportConfigurator;

    public function __construct(TransportConfigurator $transportConfigurator)
    {
        $this->transportConfigurator = $transportConfigurator;
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
        $this->transportConfigurator->bindExchange(
            Exchange::delayed($destination->exchange),
            new ExchangeBinding(Exchange::direct($message->destination->exchange)->makeDurable(), $message->destination->routingKey),
        );

        $producer->publish(new AmqpDestination($destination->exchange, $destination->routingKey), $message);
    }
}
