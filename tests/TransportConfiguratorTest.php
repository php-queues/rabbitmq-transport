<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport\Tests;

use PhpQueues\RabbitmqTransport\AmqpOperator;
use PhpQueues\RabbitmqTransport\Exchange;
use PhpQueues\RabbitmqTransport\ExchangeBinding;
use PhpQueues\RabbitmqTransport\Queue;
use PhpQueues\RabbitmqTransport\QueueBinding;
use PhpQueues\RabbitmqTransport\TransportConfigurator;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class TransportConfiguratorTest extends TestCase
{
    public function testQueueBind(): void
    {
        $queue = Queue::default('test')->makeDurable();
        $exchange = Exchange::direct('exchange')->makeDurable();

        $operator = $this->createMock(AmqpOperator::class);

        $operator
            ->expects($this->exactly(1))
            ->method('declareQueue')
            ->with($queue);

        $operator
            ->expects($this->exactly(1))
            ->method('declareExchange')
            ->with($exchange);

        $operator
            ->expects($this->exactly(1))
            ->method('bindQueue')
            ->with($queue, $exchange, 'test_routing_key');

        $configuration = new TransportConfigurator($operator);

        $configuration->bindQueue($queue, new QueueBinding($exchange, 'test_routing_key'));
    }

    public function testExchangeBind(): void
    {
        $source = Exchange::delayed('delayed')->makeDurable();
        $destination = Exchange::direct('exchange')->makeDurable();

        $operator = $this->createMock(AmqpOperator::class);

        $operator
            ->expects($this->exactly(2))
            ->method('declareExchange')
            ->withConsecutive([$this->equalTo($source)], [$this->equalTo($destination)]);

        $operator
            ->expects($this->exactly(1))
            ->method('bindExchange')
            ->with($source, $destination, 'test_routing_key');

        $configuration = new TransportConfigurator($operator);

        $configuration->bindExchange($source, new ExchangeBinding($destination, 'test_routing_key'));
    }
}
