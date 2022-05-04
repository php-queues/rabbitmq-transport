<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport;

use PhpQueues\Transport\Message;
use PhpQueues\Transport\Producer;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @psalm-template M as AmqpMessage
 *
 * @template-implements Producer<M>
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
    public function publish(Message ...$messages): void
    {
        try {
            $this->packagePublisher->publish(...$messages);
        } catch (\Throwable $e) {
            $this->logger->critical('Cannot publish packages due to an error "{error}".', [
                'error' => $e->getMessage(),
                'exception' => $e,
            ]);
        }
    }
}
