<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;

class DeleteModflowModelCommand extends Command
{

    private $id;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/deleteModflowModel.json');
    }

    /**
     * @param string $id
     * @return DeleteModflowModelCommand
     */
    public static function fromParams(string $id): self
    {
        $self = new self();
        $self->id = $id;
        return $self;
    }

    /**
     * @param array $payload
     * @return DeleteModflowModelCommand
     */
    public static function fromPayload(array $payload): self
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
