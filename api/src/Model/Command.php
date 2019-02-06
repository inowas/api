<?php

declare(strict_types=1);

namespace App\Model;

abstract class Command
{
    protected $metadata = [];

    public static function getMessageName(): string
    {
        return str_replace('Command', '', lcfirst(substr(static::class, strrpos(static::class, '\\') + 1)));
    }

    abstract public static function fromPayload(array $payload);

    public function withAddedMetadata(string $key, $value): void
    {
        $this->metadata[$key] = $value;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }
}
