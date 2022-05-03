<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport\Tests\Delay;

use PhpQueues\RabbitmqTransport\AmqpDestination;
use PhpQueues\RabbitmqTransport\AmqpMessage;
use PhpQueues\RabbitmqTransport\AmqpOperator;
use PhpQueues\RabbitmqTransport\AmqpProducer;
use PhpQueues\RabbitmqTransport\Delay\AmqpDelayDestination;
use PhpQueues\RabbitmqTransport\PackagePublisher;
use PhpQueues\RabbitmqTransport\Queue;
use PhpQueues\RabbitmqTransport\TransportConfigurator;
use PHPUnit\Framework\TestCase;
use PhpQueues\RabbitmqTransport\Delay\DelayMessageUsingDeadLetterExchange;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class DelayMessageUsingDeadLetterExchangeTest extends TestCase
{
    public function testDelayedQueueWasDeclared(): void
    {
        $operator = $this->createMock(AmqpOperator::class);

        $operator
            ->expects($this->exactly(1))
            ->method('declareQueue')
            ->with(Queue::default('tests_delay')->makeDurable()->withArguments([
                'x-message-ttl' => 5000,
                'x-expires' => 5000 * 2,
                'x-dead-letter-exchange' => '',
                'x-dead-letter-routing-key' => 'tests',
            ]))
        ;

        $publisher = $this->createMock(PackagePublisher::class);

        $message = new AmqpMessage('some_id', '{"name": "test"}', new AmqpDestination('tests', 'tests'));

        $publisher
            ->expects($this->exactly(1))
            ->method('publish')
            ->with(new AmqpDestination('', 'tests_delay'), $message);

        $transportConfigurator = new TransportConfigurator($operator);

        $delayMessageUsingDeadLetterExchange = new DelayMessageUsingDeadLetterExchange($transportConfigurator);

        $delayMessageUsingDeadLetterExchange->delay(
            new AmqpProducer($publisher),
            $message,
            new AmqpDelayDestination('tests_delay', 'tests'),
            5000
        );
    }

    public function testDelayedQueueWithCustomFormulaWasDeclared(): void
    {
        $operator = $this->createMock(AmqpOperator::class);

        $operator
            ->expects($this->exactly(1))
            ->method('declareQueue')
            ->with(Queue::default('tests_delay')->makeDurable()->withArguments([
                'x-message-ttl' => 5000,
                'x-expires' => 5000 + 1000,
                'x-dead-letter-exchange' => '',
                'x-dead-letter-routing-key' => 'tests',
            ]))
        ;

        $publisher = $this->createMock(PackagePublisher::class);

        $message = new AmqpMessage('some_id', '{"name": "test"}', new AmqpDestination('tests', 'tests'));

        $publisher
            ->expects($this->exactly(1))
            ->method('publish')
            ->with(new AmqpDestination('', 'tests_delay'), $message);

        $transportConfigurator = new TransportConfigurator($operator);

        $delayMessageUsingDeadLetterExchange = new DelayMessageUsingDeadLetterExchange($transportConfigurator, function (int $delay): int {
            return $delay + 1000;
        });

        $delayMessageUsingDeadLetterExchange->delay(
            new AmqpProducer($publisher),
            $message,
            new AmqpDelayDestination('tests_delay', 'tests'),
            5000
        );
    }

    public function testDelayedQueueWithDeadLetterExchangeWasDeclared(): void
    {
        $operator = $this->createMock(AmqpOperator::class);

        $operator
            ->expects($this->exactly(1))
            ->method('declareQueue')
            ->with(Queue::default('tests_delay')->makeDurable()->withArguments([
                'x-message-ttl' => 5000,
                'x-expires' => 5000 + 1000,
                'x-dead-letter-exchange' => 'test_exchange',
                'x-dead-letter-routing-key' => 'tests',
            ]))
        ;

        $publisher = $this->createMock(PackagePublisher::class);

        $message = new AmqpMessage('some_id', '{"name": "test"}', new AmqpDestination('tests', 'tests'));

        $publisher
            ->expects($this->exactly(1))
            ->method('publish')
            ->with(new AmqpDestination('', 'tests_delay'), $message);

        $transportConfigurator = new TransportConfigurator($operator);

        $delayMessageUsingDeadLetterExchange = new DelayMessageUsingDeadLetterExchange($transportConfigurator, function (int $delay): int {
            return $delay + 1000;
        });

        $delayMessageUsingDeadLetterExchange->delay(
            new AmqpProducer($publisher),
            $message,
            new AmqpDelayDestination('tests_delay', 'tests', 'test_exchange'),
            5000
        );
    }
}
