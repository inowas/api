<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\ToolMetadata;

class CreateToolInstanceCommand extends Command
{

    private $id;
    private $tool;

    private $name;
    private $description;
    private $public;

    private $data;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/createToolInstance.json');
    }

    /**
     * @param string $id
     * @param string $tool
     * @param string $name
     * @param string $description
     * @param bool $public
     * @param array $data
     * @return CreateToolInstanceCommand
     * @throws \Exception
     */
    public static function fromParams(string $id, string $tool, string $name, string $description, bool $public, array $data = []): CreateToolInstanceCommand
    {
        $self = new self();
        $self->id = $id;
        $self->tool = $tool;
        $self->name = $name;
        $self->description = $description;
        $self->public = $public;
        $self->data = $data;
        return $self;
    }

    /**
     * @param array $payload
     * @return CreateToolInstanceCommand
     * @throws \Exception
     */
    public static function fromPayload(array $payload): CreateToolInstanceCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->tool = $payload['tool'];
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

    public function tool(): string
    {
        return $this->tool;
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
