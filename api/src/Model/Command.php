<?php

declare(strict_types=1);

namespace App\Model;

abstract class Command
{
    protected $metadata = [];

    /** @var \DateTimeImmutable */
    protected $dateTime;

    abstract public static function fromPayload(array $payload);

    public static function getMessageName(): string
    {
        return str_replace('Command', '', lcfirst(substr(static::class, strrpos(static::class, '\\') + 1)));
    }

    public static function getJsonSchema(): ?string
    {
        return null;
    }

    /**
     * @throws \Exception
     */
    protected function __construct()
    {
        $this->dateTime = new \DateTimeImmutable('now');
    }

    public function withAddedMetadata(string $key, $value): void
    {
        $this->metadata[$key] = $value;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function dateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }
}
