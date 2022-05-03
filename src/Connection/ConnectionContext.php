<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport\Connection;

final class ConnectionContext
{
    public const DELAY_TYPE_DEAD_LETTER = 'dead_letter_exchange';
    public const DELAY_TYPE_DELAYED_EXCHANGE = 'delayed_exchange';

    /**
     * @psalm-readonly
     */
    public Dsn $dsn;

    /**
     * @psalm-readonly
     *
     * @psalm-var ConnectionContext::DELAY_TYPE_*
     */
    public string $delayType;

    /**
     * @psalm-param ConnectionContext::DELAY_TYPE_* $delayType
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(Dsn $dsn, string $delayType)
    {
        if (\in_array($delayType, [self::DELAY_TYPE_DEAD_LETTER, self::DELAY_TYPE_DELAYED_EXCHANGE]) === false) {
            throw new \InvalidArgumentException("Invalid delay type \"$delayType\".");
        }

        $this->dsn = $dsn;
        $this->delayType = $delayType;
    }
}
