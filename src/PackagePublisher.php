<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport;

interface PackagePublisher
{
    /**
     * @throws \Throwable
     */
    public function publish(AmqpMessage ...$messages): void;
}
