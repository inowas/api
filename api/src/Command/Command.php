<?php

declare(strict_types=1);

namespace App\Command;

abstract class Command
{
    /** @var array */
    protected $metadata;

    abstract public static function fromPayload(array $payload);

    public function withAddedMetadata(string $key, $value) {
        $this->metadata[$key] = $value;
    }
}
