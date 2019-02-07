<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\Command;

use App\Model\Command;

class CloneModflowModelCommand extends Command
{

    private $id;
    private $newId;
    private $isTool;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/cloneModflowModel.json');
    }

    /**
     * @param array $payload
     * @return CloneModflowModelCommand
     */
    public static function fromPayload(array $payload): CloneModflowModelCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->newId = $payload['new_id'];
        $self->isTool = $payload['is_tool'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function newId(): string
    {
        return $this->newId;
    }

    public function isTool(): bool
    {
        return $this->isTool;
    }
}
