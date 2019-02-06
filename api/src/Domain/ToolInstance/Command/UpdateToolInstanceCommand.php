<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;

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
        return 'https://schema.inowas.com/commands/updateToolInstance.json';
    }

    /**
     * @param array $payload
     * @return UpdateToolInstanceCommand
     */
    public static function fromPayload(array $payload)
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->name = $payload['name'];
        $self->description = $payload['description'];
        $self->public = $payload['public'];
        $self->data = $payload['data'] ?? [];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function data(): array
    {
        return $this->data;
    }
}
