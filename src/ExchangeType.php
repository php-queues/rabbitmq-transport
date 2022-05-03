<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport;

final class ExchangeType
{
    private const TYPE_DIRECT = 'direct';
    private const TYPE_FANOUT = 'fanout';
    private const TYPE_TOPIC = 'topic';
    private const TYPE_HEADERS = 'headers';
    private const TYPE_DELAYED = 'x-delayed-message';

    /**
     * @psalm-readonly
     *
     * @psalm-var ExchangeType::TYPE_*
     */
    public string $value;

    /**
     * @psalm-param ExchangeType::TYPE_* $value
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function direct(): ExchangeType
    {
        return new ExchangeType(self::TYPE_DIRECT);
    }

    public static function fanout(): ExchangeType
    {
        return new ExchangeType(self::TYPE_FANOUT);
    }

    public static function topic(): ExchangeType
    {
        return new ExchangeType(self::TYPE_TOPIC);
    }

    public static function headers(): ExchangeType
    {
        return new ExchangeType(self::TYPE_HEADERS);
    }

    public static function delayed(): ExchangeType
    {
        return new ExchangeType(self::TYPE_DELAYED);
    }
}
