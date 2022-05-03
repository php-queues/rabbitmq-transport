<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport\Connection;

use PhpQueues\RabbitmqTransport\AmqpDestination;
use PhpQueues\RabbitmqTransport\AmqpMessage;
use PhpQueues\RabbitmqTransport\TransportConfigurator;
use PhpQueues\Transport\Consumer;
use PhpQueues\Transport\Producer;

abstract class AmqpConnector
{
    private ConnectionContext $context;

    final private function __construct(ConnectionContext $context)
    {
        $this->context = $context;
    }

    final public static function connect(ConnectionContext $context): AmqpConnector
    {
        return new static($context);
    }

    abstract public function configurator(): TransportConfigurator;

    /**
     * @psalm-return Producer<AmqpDestination, AmqpMessage>
     */
    abstract public function producer(): Producer;
    abstract public function consumer(): Consumer;

    final protected function context(): ConnectionContext
    {
        return $this->context;
    }
}
