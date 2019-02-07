<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\Command;

use App\Model\Command;

class RemoveLayerCommand extends Command
{

    private $id;
    private $layerId;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/removeLayer.json');
    }

    /**
     * @param array $payload
     * @return RemoveLayerCommand
     */
    public static function fromPayload(array $payload): RemoveLayerCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->layerId = $payload['layer_id'];
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
}
