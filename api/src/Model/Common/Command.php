<?php

declare(strict_types=1);

namespace App\Model\Common;

abstract class Command
{
    /** @var array */
    protected $metadata = [];

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
