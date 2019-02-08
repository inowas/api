<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\Command;

use App\Model\Command;

class CreateModflowModelCommand extends Command
{

    private $id;
    private $name;
    private $description;
    private $public;

    private $geometry;
    private $boundingBox;
    private $gridSize;
    private $activeCells;
    private $stressperiods;
    private $lengthUnit;
    private $timeUnit;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/createModflowModel.json');
    }

    /**
     * @param array $payload
     * @return CreateModflowModelCommand
     */
    public static function fromPayload(array $payload): CreateModflowModelCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->name = $payload['name'];
        $self->description = $payload['description'];
        $self->geometry = $payload['geometry'];
        $self->boundingBox = $payload['bounding_box'];
        $self->gridSize = $payload['grid_size'];
        $self->activeCells = $payload['active_cells'];
        $self->stressperiods = $payload['stressperiods'];
        $self->lengthUnit = $payload['length_unit'];
        $self->timeUnit = $payload['time_unit'];
        $self->public = $payload['public'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function discretization(): array
    {
        return [
            'geometry' => $this->geometry,
            'bounding_box' => $this->boundingBox,
            'grid_size' => $this->gridSize,
            'active_cells' => $this->activeCells,
            'stressperiods' => $this->stressperiods,
            'length_unit' => $this->lengthUnit,
            'time_unit' => $this->timeUnit
        ];
    }
}
