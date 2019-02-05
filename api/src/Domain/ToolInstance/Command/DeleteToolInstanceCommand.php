<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Domain\Common\Command;

class DeleteToolInstanceCommand extends Command
{

    private $id;

    /**
     * @return string
     */
    public static function commandName(): string
    {
        return 'deleteToolInstance';
    }

    /**
     * @param array $payload
     * @return DeleteToolInstanceCommand
     */
    public static function fromPayload(array $payload)
    {
        $self = new self();
        $self->id = $payload['id'];
        return $self;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
