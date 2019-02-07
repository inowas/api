<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\Command;

use App\Model\Command;

class CreateModflowModelCommand extends Command
{

    private $id;
    private $name;
    private $description;
    private $geometry;
    private $boundingBox;
    private $gridSize;
    private $activeCells;
    private $stressperiods;
    private $lengthUnit;
    private $timeUnit;
    private $public;

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
        $self->stressperiods = $payload['stress_periods'];
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

    public function geometry(): array
    {
        return $this->geometry;
    }

    public function boundingBox(): array
    {
        return $this->boundingBox;
    }

    public function gridSize(): array
    {
        return $this->gridSize;
    }

    public function activeCells(): array
    {
        return $this->activeCells;
    }

    public function lengthUnit(): int
    {
        return $this->lengthUnit;
    }

    public function timeUnit(): int
    {
        return $this->timeUnit;
    }

    public function stressPeriods(): array
    {
        return $this->stressperiods;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }
}
