<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport;

use PhpQueues\Transport\Package;

final class AmqpPackage implements Package
{
    /**
     * @psalm-var non-empty-string
     */
    private string $id;

    /**
     * @psalm-var non-empty-string
     */
    private string $content;
    private array $headers;

    /**
     * @var callable():void
     */
    private $acknowledger;

    /**
     * @var callable(bool,?string):void
     */
    private $nacknowledger;

    /**
     * @var callable(bool,?string):void
     */
    private $rejecter;

    /**
     * @psalm-param non-empty-string $id
     * @psalm-param non-empty-string $content
     * @psalm-param callable():void $acknowledger
     * @psalm-param callable(bool,?string):void $nacknowledger
     * @psalm-param (callable(bool,?string):void)|null $rejecter
     */
    public function __construct(
        string $id,
        string $content,
        array $headers,
        callable $acknowledger,
        callable $nacknowledger,
        ?callable $rejecter = null
    ) {
        $this->id = $id;
        $this->content = $content;
        $this->headers = $headers;
        $this->acknowledger = $acknowledger;
        $this->nacknowledger = $nacknowledger;
        $this->rejecter = $rejecter ?: $nacknowledger;
    }

    /**
     * {@inheritdoc}
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function content(): string
    {
        return $this->content;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function ack(): void
    {
        ($this->acknowledger)();
    }

    public function nack(bool $requeue, ?string $withReason = null): void
    {
        ($this->nacknowledger)($requeue, $withReason);
    }

    public function reject(bool $requeue, ?string $withReason = null): void
    {
        ($this->rejecter)($requeue, $withReason);
    }
}
