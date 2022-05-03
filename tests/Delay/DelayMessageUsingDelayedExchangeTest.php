<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport\Tests\Delay;

use PhpQueues\RabbitmqTransport\AmqpDestination;
use PhpQueues\RabbitmqTransport\AmqpMessage;
use PhpQueues\RabbitmqTransport\AmqpOperator;
use PhpQueues\RabbitmqTransport\AmqpProducer;
use PhpQueues\RabbitmqTransport\Delay\AmqpDelayDestination;
use PhpQueues\RabbitmqTransport\Delay\DelayMessageUsingDelayedExchange;
use PhpQueues\RabbitmqTransport\Exchange;
use PhpQueues\RabbitmqTransport\PackagePublisher;
use PhpQueues\RabbitmqTransport\TransportConfigurator;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class DelayMessageUsingDelayedExchangeTest extends TestCase
{
    public function testDelayedExchangeWasDeclared(): void
    {
        $operator = $this->createMock(AmqpOperator::class);

        $operator
            ->expects($this->exactly(2))
            ->method('declareExchange')
            ->withConsecutive([$this->equalTo(Exchange::delayed('delay_tests'))], [$this->equalTo(Exchange::direct('tests')->makeDurable())]);

        $publisher = $this->createMock(PackagePublisher::class);

        $message = new AmqpMessage('some_id', '{"name": "test"}', new AmqpDestination('tests', 'tests'));

        $publisher
            ->expects($this->exactly(1))
            ->method('publish')
            ->with(new AmqpDestination('delay_tests', 'tests'), $message);

        $delayMessage = new DelayMessageUsingDelayedExchange(new TransportConfigurator($operator));

        $delayMessage->delay(
            new AmqpProducer($publisher),
            $message,
            new AmqpDelayDestination('tests', 'tests', 'delay_tests'),
            5000
        );
    }
}
