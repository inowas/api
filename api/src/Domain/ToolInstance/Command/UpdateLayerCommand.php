<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\Modflow\Layer;
use Exception;

class UpdateLayerCommand extends Command
{
    /** @var string */
    private $id;

    /** @var array */
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
     * @return self
     * @throws Exception
     */
    public static function fromPayload(array $payload): self
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->layer = $payload['layer'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function layer(): Layer
    {
        return Layer::fromArray($this->layer);
    }
}
