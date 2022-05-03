<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class TransportConfigurator
{
    private AmqpOperator $amqpOperator;
    private LoggerInterface $logger;

    public function __construct(AmqpOperator $amqpOperator, ?LoggerInterface $logger = null)
    {
        $this->amqpOperator = $amqpOperator;
        $this->logger = $logger ?: new NullLogger();
    }

    public function bindQueue(Queue $queue, QueueBinding ...$bindings): void
    {
        $this->amqpOperator->declareQueue($queue);

        $this->logger->debug('The queue "{queueName}" was created.', [
            'queueName' => $queue->name,
        ]);

        foreach ($bindings as $binding) {
            $this->doDeclareExchange($binding->exchange);
            $this->amqpOperator->bindQueue($queue, $binding->exchange, $binding->routingKey, $binding->arguments);

            $this->logger->debug('The queue "{queueName}" was linked to exchange "{exchangeName}" with routing key "{routingKey}".', [
                'queueName' => $queue->name,
                'exchangeName' => $binding->exchange->name,
                'routingKey' => $binding->routingKey,
            ]);
        }
    }

    public function bindExchange(Exchange $exchange, ExchangeBinding ...$bindings): void
    {
        $this->doDeclareExchange($exchange);

        $this->logger->debug('The exchange "{exchangeName}" was created.', [
            'exchangeName' => $exchange->name,
        ]);

        foreach ($bindings as $binding) {
            $this->doDeclareExchange($binding->destination);
            $this->amqpOperator->bindExchange($exchange, $binding->destination, $binding->routingKey, $binding->noWait, $binding->arguments);

            $this->logger->debug('The exchange "{sourceExchangeName}" was linked to exchange "{destinationExchangeName}" with routing key "{routingKey}".', [
                'sourceExchangeName' => $exchange->name,
                'destinationExchangeName' => $binding->destination->name,
                'routingKey' => $binding->routingKey,
            ]);
        }
    }

    private function doDeclareExchange(Exchange $exchange): void
    {
        $this->amqpOperator->declareExchange($exchange);

        $this->logger->debug('The exchange "{exchangeName}" was created.', [
            'exchangeName' => $exchange->name,
        ]);
    }
}
