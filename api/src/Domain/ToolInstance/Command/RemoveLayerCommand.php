<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use Exception;

class RemoveLayerCommand extends Command
{
    /** @var string */
    private $id;

    /** @var string */
    private $layerId;

    /**
     * @return string
     */
    public static function getJsonSchema(): string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/removeLayer.json');
    }

    /**
     * @param array $payload
     * @return self
     * @throws Exception
     */
    public static function fromPayload(array $payload): self
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
