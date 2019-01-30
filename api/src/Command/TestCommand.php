<?php

declare(strict_types=1);

namespace App\Command;


class TestCommand extends Command
{
    /** @var array */
    private $payload;

    public static function fromPayload(array $payload)
    {
        $self = new self();
        $self->payload = $payload;
        return $self;
    }
}
