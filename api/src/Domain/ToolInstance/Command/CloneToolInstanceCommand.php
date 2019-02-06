<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;

class CloneToolInstanceCommand extends Command
{
    private $id;
    private $baseId;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return 'https://schema.inowas.com/commands/cloneToolInstance.json';
    }

    /**
     * @param array $payload
     * @return CloneToolInstanceCommand
     */
    public static function fromPayload(array $payload)
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->baseId = $payload['base_id'];;
        return $self;
    }

    /**
     * The id which the clone will have
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * The id of the tool which will be cloned
     * @return string
     */
    public function baseId(): string
    {
        return $this->baseId;
    }
}
