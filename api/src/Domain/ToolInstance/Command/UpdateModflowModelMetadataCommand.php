<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\ToolMetadata;

class UpdateModflowModelMetadataCommand extends Command
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
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/updateModflowModelMetadata.json');
    }

    /**
     * @param array $payload
     * @return UpdateModflowModelMetadataCommand
     */
    public static function fromPayload(array $payload): UpdateModflowModelMetadataCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->name = $payload['name'];
        $self->description = $payload['description'];
        $self->public = $payload['public'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function toolMetadata(): ToolMetadata
    {
        return ToolMetadata::fromArray([
            'name' => $this->name,
            'description' => $this->description,
            'public' => $this->public
        ]);
    }
}
