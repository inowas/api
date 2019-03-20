<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\ToolMetadata;

class UpdateToolInstanceMetadataCommand extends Command
{

    private $id;
    private $name;
    private $description;
    private $public;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/updateToolInstanceMetadata.json');
    }

    /**
     * @param string $id
     * @param string $name
     * @param string $description
     * @param bool $public
     * @return UpdateToolInstanceMetadataCommand
     * @throws \Exception
     */
    public static function fromParams(string $id, string $name, string $description, bool $public): UpdateToolInstanceMetadataCommand
    {
        $self = new self();
        $self->id = $id;
        $self->name = $name;
        $self->description = $description;
        $self->public = $public;
        return $self;
    }

    /**
     * @param array $payload
     * @return UpdateToolInstanceMetadataCommand
     * @throws \Exception
     */
    public static function fromPayload(array $payload): UpdateToolInstanceMetadataCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->name = $payload['name'];
        $self->description = $payload['description'] ?? null;
        $self->public = $payload['public'] ?? null;
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
}
