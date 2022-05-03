<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport\Tests;

use PhpQueues\RabbitmqTransport\AmqpDestination;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class AmqpDestinationTest extends TestCase
{
    public function testExchangeName(): void
    {
        self::assertEquals('exchange', (new AmqpDestination('exchange', ''))->exchange);
    }
}
