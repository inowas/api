<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;

class DeleteToolInstanceCommand extends Command
{

    private $id;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return 'https://schema.inowas.com/commands/deleteToolInstance.json';
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

    public function id(): string
    {
        return $this->id;
    }
}
