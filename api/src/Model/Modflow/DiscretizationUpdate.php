<?php

namespace App\Model\Modflow;

final class DiscretizationUpdate
{

    private $geometry;
    private $boundingBox;
    private $gridSize;
    private $activeCells;
    private $stressperiods;
    private $lengthUnit;
    private $timeUnit;

    public static function fromParams(?array $geometry, ?array $boundingBox, ?array $gridSize, ?array $activeCells, ?array $stressperiods, ?int $lengthUnit, ?int $timeUnit): DiscretizationUpdate
    {
        $self = new self();
        $self->geometry = $geometry;
        $self->boundingBox = $boundingBox;
        $self->gridSize = $gridSize;
        $self->activeCells = $activeCells;
        $self->stressperiods = $stressperiods;
        $self->lengthUnit = $lengthUnit;
        $self->timeUnit = $timeUnit;
        return $self;
    }

    public static function fromArray(array $arr): DiscretizationUpdate
    {
        $self = new self();
        $self->geometry = $arr['geometry'] ?? null;
        $self->boundingBox = $arr['bounding_box'] ?? null;
        $self->gridSize = $arr['grid_size'] ?? null;
        $self->activeCells = $arr['active_cells'] ?? null;
        $self->stressperiods = $arr['stressperiods'] ?? null;
        $self->lengthUnit = $arr['length_unit'] ?? null;
        $self->timeUnit = $arr['time_unit'] ?? null;
        return $self;
    }

    private function __construct()
    {
    }

    public function getGeometry(): ?array
    {
        return $this->geometry;
    }

    public function getBoundingBox(): ?array
    {
        return $this->boundingBox;
    }

    public function getGridSize(): ?array
    {
        return $this->gridSize;
    }

    public function getActiveCells(): ?array
    {
        return $this->activeCells;
    }

    public function getStressperiods(): ?array
    {
        return $this->stressperiods;
    }

    public function getLengthUnit(): ?int
    {
        return $this->lengthUnit;
    }

    public function getTimeUnit(): ?int
    {
        return $this->timeUnit;
    }

    public function toArray(): array
    {
        $arr = [
            'geometry' => $this->geometry,
            'bounding_box' => $this->boundingBox,
            'grid_size' => $this->gridSize,
            'active_cells' => $this->activeCells,
            'stressperiods' => $this->stressperiods,
            'length_unit' => $this->lengthUnit,
            'time_unit' => $this->timeUnit
        ];

        foreach ($arr as $key => $value) {
            if (null === $value) {
                unset($arr[$value]);
            }
        }

        return $arr;
    }

    public function getDiff(Discretization $discretization): DiscretizationUpdate
    {
        $geometry = ($this->geometry !== null || $this->geometry !== $discretization->getGeometry()) ? $this->geometry : null;
        $boundingBox = ($this->boundingBox !== null || $this->boundingBox !== $discretization->getBoundingBox()) ? $this->boundingBox : null;
        $gridSize = ($this->gridSize !== null || $this->gridSize !== $discretization->getGridSize()) ? $this->gridSize : null;
        $activeCells = ($this->activeCells !== null || $this->activeCells !== $discretization->getActiveCells()) ? $this->activeCells : null;
        $stressperiods = ($this->stressperiods !== null || $this->stressperiods !== $discretization->getStressperiods()) ? $this->stressperiods : null;
        $lengthUnit = ($this->lengthUnit !== null || $this->lengthUnit !== $discretization->getLengthUnit()) ? $this->lengthUnit : null;
        $timeUnit = ($this->timeUnit !== null || $this->timeUnit !== $discretization->getTimeUnit()) ? $this->timeUnit : null;
        return DiscretizationUpdate::fromParams($geometry, $boundingBox, $gridSize, $activeCells, $stressperiods, $lengthUnit, $timeUnit);
    }

    public function hasContent(): bool
    {
        return count($this->toArray()) !== 0;
    }
}
