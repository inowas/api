<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;

class UpdateToolInstanceDataCommand extends Command
{

    private $id;
    private $data;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/updateToolInstanceData.json');
    }

    /**
     * @param string $id
     * @param array $data
     * @return UpdateToolInstanceDataCommand
     * @throws \Exception
     */
    public static function fromParams(string $id, array $data): UpdateToolInstanceDataCommand
    {
        $self = new self();
        $self->id = $id;
        $self->data = $data;
        return $self;
    }

    /**
     * @param array $payload
     * @return UpdateToolInstanceDataCommand
     * @throws \Exception
     */
    public static function fromPayload(array $payload): UpdateToolInstanceDataCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->data = $payload['data'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function data(): array
    {
        return $this->data;
    }
}
