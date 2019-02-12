<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\ToolMetadata;

class UpdateModflowModelMetadataCommand extends Command
{

    private $id;
    private $toolMetadata;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/updateModflowModelMetadata.json');
    }

    /**
     * @param array $payload
     * @return self
     */
    public static function fromPayload(array $payload): self
    {
        $self = new self();
        $self->id = $payload['id'];

        $self->toolMetadata = [
            'name' => $payload['name'] ?? null,
            'description' => $payload['description'] ?? null,
            'public' => $payload['public'] ?? null,
        ];

        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function toolMetadata(): ToolMetadata
    {
        return ToolMetadata::fromArray($this->toolMetadata);
    }
}
