<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport\Tests;

use PhpQueues\RabbitmqTransport\Exchange;
use PhpQueues\RabbitmqTransport\ExchangeType;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class ExchangeTest extends TestCase
{
    public function testEmptyDirectExchangeName(): void
    {
        self::expectException(\InvalidArgumentException::class);
        Exchange::direct('');
    }

    public function testEmptyFanoutExchangeName(): void
    {
        self::expectException(\InvalidArgumentException::class);
        Exchange::fanout('');
    }

    public function testEmptyTopicExchangeName(): void
    {
        self::expectException(\InvalidArgumentException::class);
        Exchange::topic('');
    }

    public function testEmptyDelayedExchangeName(): void
    {
        self::expectException(\InvalidArgumentException::class);
        Exchange::delayed('');
    }

    public function testEmptyHeadersExchangeName(): void
    {
        self::expectException(\InvalidArgumentException::class);
        Exchange::headers('');
    }

    /**
     * @psalm-return \Generator<array{Exchange, ExchangeType}>
     */
    public function provideExchangeAndTypes(): \Generator
    {
        yield [Exchange::direct('test'), ExchangeType::direct()];
        yield [Exchange::fanout('test'), ExchangeType::fanout()];
        yield [Exchange::topic('test'), ExchangeType::topic()];
        yield [Exchange::headers('test'), ExchangeType::headers()];
        yield [Exchange::delayed('test'), ExchangeType::delayed()];
    }

    /**
     * @dataProvider provideExchangeAndTypes
     */
    public function testExchangeTypes(Exchange $exchange, ExchangeType $exchangeType): void
    {
        self::assertEquals($exchange->type->value, $exchangeType->value);
        self::assertEquals('test', $exchange->name);
    }

    public function testDelayedExchange(): void
    {
        $exchange = Exchange::delayed('test');
        self::assertTrue($exchange->durable);
        self::assertEquals(['x-delayed-type' => ExchangeType::direct()->value], $exchange->arguments);
    }

    public function testDefaultExchange(): void
    {
        $exchange = Exchange::direct('test');
        self::assertFalse($exchange->durable);
        self::assertFalse($exchange->internal);
        self::assertFalse($exchange->passive);
        self::assertFalse($exchange->noWait);
        self::assertFalse($exchange->autoDelete);
        self::assertEmpty($exchange->arguments);
    }

    public function testDurableExchange(): void
    {
        self::assertTrue(Exchange::direct('test')->makeDurable()->durable);
    }

    public function testPassiveExchange(): void
    {
        self::assertTrue(Exchange::direct('test')->makePassive()->passive);
    }

    public function testInternalExchange(): void
    {
        self::assertTrue(Exchange::direct('test')->makeInternal()->internal);
    }

    public function testAutoDeleteExchange(): void
    {
        self::assertTrue(Exchange::direct('test')->makeAutoDelete()->autoDelete);
    }

    public function testNoWaitExchange(): void
    {
        self::assertTrue(Exchange::direct('test')->noWait()->noWait);
    }

    public function testExchangeWithArguments(): void
    {
        $exchange = Exchange::direct('test')->withArguments(['x-key' => 'x-value']);
        self::assertEquals(['x-key' => 'x-value'], $exchange->arguments);

        $exchange = $exchange->withArguments(['x-another-key' => 'x-another-value']);

        self::assertEquals(['x-key' => 'x-value', 'x-another-key' => 'x-another-value'], $exchange->arguments);
    }
}
