<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport\Tests;

use PhpQueues\RabbitmqTransport\AmqpDestination;
use PhpQueues\RabbitmqTransport\AmqpMessage;
use PhpQueues\RabbitmqTransport\AmqpProducer;
use PhpQueues\RabbitmqTransport\PackagePublisher;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class AmqpProducerTest extends TestCase
{
    public function testMessagePublished(): void
    {
        /** @psalm-var non-empty-string */
        $id = \uniqid();

        $destination = new AmqpDestination('test_exchange', 'test_routing_key');
        $message = new AmqpMessage($id, '{"name": "test"}', $destination);

        $publisher = $this->createMock(PackagePublisher::class);

        $publisher->expects($this->exactly(1))
            ->method('publish')
            ->with($message->changeDestination($destination));

        $producer = new AmqpProducer($publisher);
        $producer->publish($message);
    }
}
