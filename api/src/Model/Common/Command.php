<?php

declare(strict_types=1);

namespace App\Model\Common;

abstract class Command
{
    /** @var array */
    protected $metadata = [];

    public static function name(): string
    {
        $classShortName = substr(static::class, strrpos(static::class, '\\') + 1);
        return lcfirst(str_replace('Command', '', $classShortName));
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
