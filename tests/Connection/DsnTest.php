<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport\Tests\Connection;

use PhpQueues\RabbitmqTransport\Connection\Dsn;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class DsnTest extends TestCase
{
    /**
     * @psalm-return \Generator<array{string, array}>
     */
    public function provideDsnString(): \Generator
    {
        yield ['amqp://localhost', ['scheme' => 'amqp', 'host' => 'localhost', 'port' => 5672]];
        yield ['amqp://jojo:secret@rabbit:15672', ['scheme' => 'amqp', 'user' => 'jojo', 'password' => 'secret', 'host' => 'rabbit', 'port' => 15672]];
        yield ['amqp://jojo:secret@rabbit:15672?vhost=/develop&timeout=60000&heartbeat=1000', ['scheme' => 'amqp', 'user' => 'jojo', 'password' => 'secret', 'host' => 'rabbit', 'port' => 15672, 'vhost' => '/develop', 'timeout' => 60000, 'heartbeat' => 1000]];
    }

    /**
     * @dataProvider provideDsnString
     *
     * @psalm-param non-empty-string $dsnString
     */
    public function testDsnFromString(string $dsnString, array $options): void
    {
        $dsn = Dsn::fromString($dsnString);

        self::assertEquals($dsnString, $dsn->connectionString);
        self::assertEquals($options, $dsn->parsedParameters);
    }

    /**
     * @psalm-return \Generator<array{string, array}>
     */
    public function provideDsnArray(): \Generator
    {
        yield ['amqp://localhost:5672', ['scheme' => 'amqp', 'host' => 'localhost', 'port' => 5672]];
        yield ['amqp://jojo:secret@rabbit:15672', ['scheme' => 'amqp', 'user' => 'jojo', 'password' => 'secret', 'host' => 'rabbit', 'port' => 15672]];
        yield ['amqp://jojo:secret@rabbit:15672?vhost=/develop&timeout=60000&heartbeat=1000', ['scheme' => 'amqp', 'user' => 'jojo', 'password' => 'secret', 'host' => 'rabbit', 'port' => 15672, 'vhost' => '/develop', 'timeout' => 60000, 'heartbeat' => 1000]];
    }

    /**
     * @dataProvider provideDsnArray
     */
    public function testDsnFromArray(string $dsnString, array $options): void
    {
        $dsn = Dsn::fromArray($options);

        self::assertEquals($dsnString, \str_replace('%2F', '/', $dsn->connectionString));
        self::assertEquals($options, $dsn->parsedParameters);
    }

    /**
     * @psalm-return \Generator<array>
     */
    public function provideInvalidOptions(): \Generator
    {
        yield [['scheme' => 'amqp', 'host' => 'localhost']];
        yield [['scheme' => 'amqp', 'host' => 1]];
        yield [['scheme' => 'amqp', 'host' => 'localhost', 'port' => '']];
    }

    /**
     * @dataProvider provideInvalidOptions
     */
    public function testInvalidOption(array $options): void
    {
        self::expectException(\InvalidArgumentException::class);
        Dsn::fromArray($options);
    }
}
