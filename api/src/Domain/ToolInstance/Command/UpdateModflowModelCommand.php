<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\Modflow\DiscretizationUpdate;
use App\Model\ToolMetadata;

class UpdateModflowModelCommand extends Command
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
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/updateModflowModel.json');
    }

    /**
     * @param array $payload
     * @return UpdateModflowModelCommand
     */
    public static function fromPayload(array $payload): UpdateModflowModelCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->name = $payload['name'] ?? null;
        $self->description = $payload['description'] ?? null;
        $self->public = $payload['public'] ?? null;

        $self->geometry = $payload['geometry'] ?? null;
        $self->boundingBox = $payload['bounding_box'] ?? null;
        $self->gridSize = $payload['grid_size'] ?? null;
        $self->activeCells = $payload['active_cells'] ?? null;
        $self->stressperiods = $payload['stressperiods'] ?? null;
        $self->lengthUnit = $payload['length_unit'] ?? null;
        $self->timeUnit = $payload['time_unit'] ?? null;
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

    public function discretization(): DiscretizationUpdate
    {
        return DiscretizationUpdate::fromArray([
            'geometry' => $this->geometry,
            'bounding_box' => $this->boundingBox,
            'grid_size' => $this->gridSize,
            'active_cells' => $this->activeCells,
            'stressperiods' => $this->stressperiods,
            'length_unit' => $this->lengthUnit,
            'time_unit' => $this->timeUnit
        ]);
    }
}
