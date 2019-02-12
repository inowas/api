<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\ToolMetadata;

class UpdateToolInstanceCommand extends Command
{

    private $id;
    private $name;
    private $description;
    private $public;
    private $data;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/updateToolInstance.json');
    }

    /**
     * @param string $id
     * @param string $name
     * @param string $description
     * @param bool $public
     * @param array $data
     * @return UpdateToolInstanceCommand
     */
    public static function fromParams(string $id, string $name, string $description, bool $public, array $data = []): UpdateToolInstanceCommand
    {
        $self = new self();
        $self->id = $id;
        $self->name = $name;
        $self->description = $description;
        $self->public = $public;
        $self->data = $data;
        return $self;
    }

    /**
     * @param array $payload
     * @return UpdateToolInstanceCommand
     */
    public static function fromPayload(array $payload): UpdateToolInstanceCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->name = $payload['name'];
        $self->description = $payload['description'];
        $self->public = $payload['public'];
        $self->data = $payload['data'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function toolMetadata(): ToolMetadata
    {
        return ToolMetadata::fromParams($this->name, $this->description, $this->public);
    }

    public function data(): array
    {
        return $this->data;
    }
}
