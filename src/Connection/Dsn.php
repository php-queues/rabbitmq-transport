<?php

declare(strict_types=1);

namespace PhpQueues\RabbitmqTransport\Connection;

final class Dsn
{
    /**
     * @psalm-var string[]
     */
    private static array $requiredOptions = [
        'scheme' => 'string',
        'host' => 'string',
        'port' => 'int',
    ];

    /**
     * @psalm-readonly
     *
     * @psalm-var non-empty-string
     */
    public string $connectionString;

    /**
     * @psalm-readonly
     *
     * @psalm-var array{scheme:string, host:string, port:positive-int, user?:string, password?:string, vhost?:string, timeout?:int, heartbeat?:int}
     */
    public array $parsedParameters;

    /**
     * @psalm-param array{scheme:string, host:string, port:positive-int, user?:string, password?:string, vhost?:string, timeout?:int, heartbeat?:int} $parsedParameters
     */
    private function __construct(string $connectionString, array $parsedParameters)
    {
        if ('' === $connectionString) {
            throw new \InvalidArgumentException('Dsn string cannot be empty.');
        }

        $this->connectionString = $connectionString;
        $this->parsedParameters = $parsedParameters;
    }

    /**
     * @psalm-param array{scheme:string, host:string, port:positive-int, user?:string, password?:string, vhost?:string, timeout?:int, heartbeat?:int} $options
     *
     * @throws \InvalidArgumentException
     */
    public static function fromArray(array $options): Dsn
    {
        self::validateOptions($options);

        $parameters = $options;

        if (isset($options['user'], $options['password'])) {
            /** @psalm-var non-empty-string $dsnString */
            $dsnString = \vsprintf('%s://%s:%s@%s:%d', [
                $options['scheme'],
                $options['user'],
                $options['password'],
                $options['host'],
                $options['port'],
            ]);
        } else {
            /** @psalm-var non-empty-string $dsnString */
            $dsnString = \vsprintf('%s://%s:%d', [
                $options['scheme'],
                $options['host'],
                $options['port'],
            ]);
        }

        unset(
            $options['scheme'],
            $options['host'],
            $options['port'],
            $options['user'],
            $options['password'],
        );

        if (\count($options) > 0) {
            $dsnString .= '?'.\http_build_query($options);
        }

        return new Dsn($dsnString, $parameters);
    }

    /**
     * @psalm-param non-empty-string $connectionString
     *
     * @throws \InvalidArgumentException
     */
    public static function fromString(string $connectionString): Dsn
    {
        $parsed = \parse_url($connectionString);

        if (false === $parsed) {
            throw new \InvalidArgumentException("Invalid connection string \"$connectionString\" provided.");
        }

        /** @psalm-var array{vhost?:string, timeout?:int, heartbeat?:int} $query */
        $query = [];

        if (isset($parsed['query'])) {
            \parse_str($parsed['query'], $query);
        }

        return new Dsn($connectionString, self::withoutNulls([
            'scheme' => $parsed['scheme'] ?? 'amqp',
            'user' => $parsed['user'] ?? null,
            'password' => $parsed['pass'] ?? null,
            'host' => $parsed['host'] ?? 'localhost',
            'port' => $parsed['port'] ?? 5672,
        ] + $query));
    }

    /**
     * @psalm-return array{scheme:string, host:string, port:positive-int, user?:string, password?:string, vhost?:string, timeout?:int, heartbeat?:int}
     */
    private static function withoutNulls(array $parsedParameters): array
    {
        /** @psalm-var array{scheme:string, host:string, port:positive-int, user?:string, password?:string, vhost?:string, timeout?:int, heartbeat?:int} */
        return \array_filter($parsedParameters, fn ($value): bool => \is_null($value) === false);
    }

    /**
     * @psalm-param array{scheme:string, host:string, port:positive-int, user?:string, password?:string, vhost?:string, timeout?:int, heartbeat?:int} $options
     *
     * @throws \InvalidArgumentException
     */
    private static function validateOptions(array $options): void
    {
        foreach (self::$requiredOptions as $optionName => $optionType) {
            if (isset($options[$optionName]) === false || self::validateOptionType($options[$optionName], $optionType) === false) {
                throw new \InvalidArgumentException("The option \"$optionName\" is not set or has invalid type.");
            }
        }
    }

    /**
     * @psalm-param string|int $option
     */
    private static function validateOptionType($option, string $type): bool
    {
        switch ($type) {
            case 'string':
                return \is_string($option);
            case 'int':
                return \is_numeric($option);
        }

        throw new \InvalidArgumentException("Unknown option type \"$type\".");
    }
}
