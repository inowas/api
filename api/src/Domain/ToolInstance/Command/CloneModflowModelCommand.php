<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use Exception;

class CloneModflowModelCommand extends Command
{
    /** @var string */
    private $id;

    /** @var string */
    private $newId;

    /** @var bool */
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
     * @throws Exception
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
