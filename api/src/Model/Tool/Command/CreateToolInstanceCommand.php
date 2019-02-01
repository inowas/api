<?php

declare(strict_types=1);

namespace App\Model\Tool\Command;

use App\Model\Common\Command;

class CreateToolInstanceCommand extends Command
{

    private $id;
    private $type;
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
        $self->type = $payload['type'];
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

    public function getType(): string
    {
        return $this->type;
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
