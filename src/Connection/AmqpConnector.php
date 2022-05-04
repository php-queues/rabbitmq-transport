<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport\Connection;

use PhpQueues\RabbitmqTransport\AmqpMessage;
use PhpQueues\RabbitmqTransport\AmqpProducer;
use PhpQueues\RabbitmqTransport\TransportConfigurator;
use PhpQueues\Transport\Consumer;
use PhpQueues\Transport\Delay\DelayMessage;
use PhpQueues\Transport\Producer;
use Psr\Log\LoggerInterface;
use PhpQueues\RabbitmqTransport\Delay\DelayMessageUsingDelayedExchange;
use PhpQueues\RabbitmqTransport\Delay\DelayMessageUsingDeadLetterExchange;

abstract class AmqpConnector
{
    private ConnectionContext $context;
    private LoggerInterface $logger;

    final private function __construct(ConnectionContext $context, LoggerInterface $logger)
    {
        $this->context = $context;
        $this->logger = $logger;
    }

    final public static function connect(ConnectionContext $context, LoggerInterface $logger): AmqpConnector
    {
        return new static($context, $logger);
    }

    abstract public function configurator(): TransportConfigurator;

    /**
     * @psalm-return AmqpProducer<AmqpMessage>
     */
    abstract public function producer(): Producer;
    abstract public function consumer(): Consumer;

    final public function delayer(): DelayMessage
    {
        switch ($this->context->delayType) {
            case ConnectionContext::DELAY_TYPE_DEAD_LETTER:
                return new DelayMessageUsingDeadLetterExchange($this->configurator());
            case ConnectionContext::DELAY_TYPE_DELAYED_EXCHANGE:
                return new DelayMessageUsingDelayedExchange($this->configurator());
        }

        throw new \InvalidArgumentException("Invalid delay type \"{$this->context->delayType}\" provided.");
    }

    final protected function context(): ConnectionContext
    {
        return $this->context;
    }

    final protected function logger(): LoggerInterface
    {
        return $this->logger;
    }
}
