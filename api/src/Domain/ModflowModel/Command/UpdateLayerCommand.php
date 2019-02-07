<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\Command;

use App\Model\Command;

class UpdateLayerCommand extends Command
{

    private $id;
    private $layerId;
    private $layer;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/updateLayer.json');
    }

    /**
     * @param array $payload
     * @return UpdateLayerCommand
     */
    public static function fromPayload(array $payload): UpdateLayerCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->layerId = $payload['layer_id'];
        $self->layer = $payload['layer'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function layerId(): string
    {
        return $this->layerId;
    }

    public function layer(): array
    {
        return $this->layer;
    }
}
