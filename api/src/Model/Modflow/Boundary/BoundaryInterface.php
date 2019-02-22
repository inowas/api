<?php

declare(strict_types=1);

namespace App\Model\Modflow\Boundary;

use GeoJson\Geometry\Geometry;

interface BoundaryInterface
{
    public function id(): string;

    public function type(): string;

    public function name(): string;

    public function geometry(): Geometry;

    public function toArray(): array;
}
