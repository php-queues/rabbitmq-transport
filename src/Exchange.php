<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport;

final class Exchange
{
    /**
     * @psalm-readonly
     *
     * @psalm-var non-empty-string
     */
    public string $name;

    /**
     * @psalm-readonly
     */
    public ExchangeType $type;

    /**
     * @psalm-readonly
     */
    public bool $passive = false;

    /**
     * @psalm-readonly
     */
    public bool $durable = false;

    /**
     * @psalm-readonly
     */
    public bool $autoDelete = false;

    /**
     * @psalm-readonly
     */
    public bool $internal = false;

    /**
     * @psalm-readonly
     */
    public bool $noWait = false;

    /**
     * @psalm-readonly
     */
    public array $arguments = [];

    private function __construct(string $name, ExchangeType $type, array $arguments = [])
    {
        if ('' === $name) {
            throw new \InvalidArgumentException('Exchange name cannot be an empty string.');
        }

        $this->name = $name;
        $this->type = $type;
        $this->arguments = $arguments;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function direct(string $name): Exchange
    {
        return new self($name, ExchangeType::direct());
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function fanout(string $name): Exchange
    {
        return new self($name, ExchangeType::fanout());
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function topic(string $name): Exchange
    {
        return new self($name, ExchangeType::topic());
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function delayed(string $name): Exchange
    {
        return (new self($name, ExchangeType::delayed(), [
            'x-delayed-type' => ExchangeType::direct()->value
        ]))->makeDurable();
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function headers(string $name): Exchange
    {
        return new self($name, ExchangeType::headers());
    }

    public function makePassive(): Exchange
    {
        $exchange = clone $this;
        $exchange->passive = true;

        return $exchange;
    }

    public function makeDurable(): Exchange
    {
        $exchange = clone $this;
        $exchange->durable = true;

        return $exchange;
    }

    public function makeAutoDelete(): Exchange
    {
        $exchange = clone $this;
        $exchange->autoDelete = true;

        return $exchange;
    }

    public function makeInternal(): Exchange
    {
        $exchange = clone $this;
        $exchange->internal = true;

        return $exchange;
    }

    public function noWait(): Exchange
    {
        $exchange = clone $this;
        $exchange->noWait = true;

        return $exchange;
    }

    public function withArguments(array $arguments): Exchange
    {
        $exchange = clone $this;
        $exchange->arguments = \array_merge($exchange->arguments, $arguments);

        return $exchange;
    }
}
