<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport\Tests;

use PhpQueues\RabbitmqTransport\Queue;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class QueueTest extends TestCase
{
    public function testEmptyQueue(): void
    {
        self::expectException(\InvalidArgumentException::class);
        Queue::default('');
    }

    public function testDefaultQueue(): void
    {
        $queue = Queue::default('test');

        self::assertFalse($queue->noWait);
        self::assertFalse($queue->autoDelete);
        self::assertFalse($queue->exclusive);
        self::assertFalse($queue->passive);
        self::assertFalse($queue->durable);
        self::assertEmpty($queue->arguments);
        self::assertEquals('test', $queue->name);
    }

    public function testDurableQueue(): void
    {
        self::assertTrue(Queue::default('test')->makeDurable()->durable);
    }

    public function testAutoDeleteQueue(): void
    {
        self::assertTrue(Queue::default('test')->makeAutoDelete()->autoDelete);
    }

    public function testExclusiveQueue(): void
    {
        self::assertTrue(Queue::default('test')->makeExclusive()->exclusive);
    }

    public function testPassiveQueue(): void
    {
        self::assertTrue(Queue::default('test')->makePassive()->passive);
    }

    public function testNoWaitQueue(): void
    {
        self::assertTrue(Queue::default('test')->noWait()->noWait);
    }

    public function testQueueWithArguments(): void
    {
        self::assertEquals(['x-key' => 'x-value'], Queue::default('test')->withArguments(['x-key' => 'x-value'])->arguments);
    }
}
