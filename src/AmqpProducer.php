<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport;

use PhpQueues\Transport\Destination;
use PhpQueues\Transport\Message;
use PhpQueues\Transport\Producer;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @psalm-template D as AmqpDestination
 * @psalm-template M as AmqpMessage
 *
 * @template-implements Producer<D, M>
 */
final class AmqpProducer implements Producer
{
    private PackagePublisher $packagePublisher;
    private LoggerInterface $logger;

    public function __construct(PackagePublisher $packagePublisher, ?LoggerInterface $logger = null)
    {
        $this->packagePublisher = $packagePublisher;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function publish(Destination $destination, Message ...$messages): void
    {
        try {
            $this->packagePublisher->publish($destination, ...$messages);
        } catch (\Throwable $e) {
            $this->logger->critical('Cannot publish package to exchange "{exchangeName}" with routing key "{routingKey}" due to an error "{error}".', [
                'exchangeName' => $destination->exchange,
                'routingKey' => $destination->routingKey,
                'error' => $e->getMessage(),
                'exception' => $e,
            ]);
        }
    }
}
