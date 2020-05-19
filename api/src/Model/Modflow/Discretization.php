<?php

namespace App\Model\Modflow;

use App\Model\ValueObject;

final class Discretization extends ValueObject
{
    private $geometry;
    private $boundingBox;
    private $gridSize;
    private $cells;
    private $stressperiods;
    private $lengthUnit;
    private $timeUnit;
    private $rotation;
    private $intersection;

    public static function fromParams(array $geometry, array $boundingBox, array $gridSize, array $cells, array $stressperiods, int $lengthUnit, int $timeUnit, ?float $rotation = 0.0, ?float $intersection = 0.0): Discretization
    {
        $self = new self();
        $self->geometry = $geometry;
        $self->boundingBox = $boundingBox;
        $self->gridSize = $gridSize;
        $self->cells = $cells;
        $self->stressperiods = $stressperiods;
        $self->lengthUnit = $lengthUnit;
        $self->timeUnit = $timeUnit;
        $self->rotation = $rotation;
        $self->intersection = $intersection;
        return $self;
    }

    public static function fromArray(array $arr): Discretization
    {
        $self = new self();
        $self->geometry = $arr['geometry'];
        $self->boundingBox = $arr['bounding_box'];
        $self->gridSize = $arr['grid_size'];
        $self->cells = $arr['cells'];
        $self->stressperiods = $arr['stressperiods'];
        $self->lengthUnit = $arr['length_unit'];
        $self->timeUnit = $arr['time_unit'];
        $self->rotation = $arr['rotation'] ?? 0.0;
        $self->intersection = $arr['intersection'] ?? 0.0;
        return $self;
    }

    private function __construct()
    {
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

    public function cells(): array
    {
        return $this->cells;
    }

    public function stressperiods(): array
    {
        return $this->stressperiods;
    }

    public function setStressperiods(array $stressperiods): void
    {
        $this->stressperiods = $stressperiods;
    }

    public function lengthUnit(): int
    {
        return $this->lengthUnit;
    }

    public function timeUnit(): int
    {
        return $this->timeUnit;
    }

    public function rotation(): float
    {
        return $this->rotation;
    }

    public function intersection(): float
    {
        return $this->intersection;
    }

    public function toArray(): array
    {
        return [
            'geometry' => $this->geometry,
            'bounding_box' => $this->boundingBox,
            'grid_size' => $this->gridSize,
            'cells' => $this->cells,
            'stressperiods' => $this->stressperiods,
            'length_unit' => $this->lengthUnit,
            'time_unit' => $this->timeUnit,
            'rotation' => $this->rotation,
            'intersection' => $this->intersection,
        ];
    }
}
