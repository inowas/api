<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Domain\Common\Command;

class UpdateToolInstanceCommand extends Command
{

    private $id;
    private $name;
    private $description;
    private $public;
    private $data;

    /**
     * @return string
     */
    public static function commandName(): string
    {
        return 'updateToolInstance';
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

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPublic(): bool
    {
        return $this->public;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
