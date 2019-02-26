<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;

class CloneLayerCommand extends Command
{
    private $id;
    private $layerId;
    private $newLayerId;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/cloneLayer.json');
    }

    /**
     * @param array $payload
     * @return self
     */
    public static function fromPayload(array $payload): self
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->layerId = $payload['layer_id'];;
        $self->newLayerId = $payload['new_layer_id'];;
        return $self;
    }

    /**
     * The id which the clone will have
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function layerId(): string
    {
        return $this->layerId;
    }

    /**
     * @return string
     */
    public function newLayerId(): string
    {
        return $this->newLayerId;
    }
}
