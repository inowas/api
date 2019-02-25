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

    public static function fromParams(array $geometry, array $boundingBox, array $gridSize, array $cells, array $stressperiods, int $lengthUnit, int $timeUnit): Discretization
    {
        $self = new self();
        $self->geometry = $geometry;
        $self->boundingBox = $boundingBox;
        $self->gridSize = $gridSize;
        $self->cells = $cells;
        $self->stressperiods = $stressperiods;
        $self->lengthUnit = $lengthUnit;
        $self->timeUnit = $timeUnit;
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

    public function toArray(): array
    {
        return [
            'geometry' => $this->geometry,
            'bounding_box' => $this->boundingBox,
            'grid_size' => $this->gridSize,
            'cells' => $this->cells,
            'stressperiods' => $this->stressperiods,
            'length_unit' => $this->lengthUnit,
            'time_unit' => $this->timeUnit
        ];
    }
}
