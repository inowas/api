<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;

class CreateToolInstanceCommand extends Command
{

    private $id;
    private $tool;
    private $name;
    private $description;
    private $public;
    private $data;

    /**
     * @return string
     */
    public static function commandName(): string
    {
        return 'createToolInstance';
    }

    /**
     * @param array $payload
     * @return CreateToolInstanceCommand
     */
    public static function fromPayload(array $payload)
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->tool = $payload['tool'];
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

    public function tool(): string
    {
        return $this->tool;
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
