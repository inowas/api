<?php

namespace App\Model\Modflow\Boundary;

use GeoJson\Feature\Feature;
use GeoJson\Geometry\Geometry;

final class RechargeBoundary extends Feature implements BoundaryInterface
{

    const TYPE = 'rch';

    /**
     * @param array $arr
     * @return RechargeBoundary
     * @throws \Exception
     */
    public static function fromArray(array $arr): self
    {
        /** @var Feature $feature */
        $self = self::jsonUnserialize($arr);

        if (!$self instanceof Feature) {
            throw new \Exception('Invalid json, expecting type feature.');
        }

        /** @noinspection PhpParamsInspection */
        return new static($self->getGeometry(), $self->getProperties(), $self->getId());
    }

    public function id(): string
    {
        return $this->getId();
    }

    public function geometry(): Geometry
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getGeometry();
    }

    public function name(): string
    {
        return $this->getProperties()['name'];
    }

    public function cells(): array
    {
        return $this->getProperties()['cells'];
    }

    public function layers(): array
    {
        return $this->getProperties()['layers'];
    }

    public function spValues(): array
    {
        return $this->getProperties()['sp_values'];
    }

    public function wellType(): string
    {
        return $this->getProperties()['well_type'];
    }

    public function type(): string
    {
        return self::TYPE;
    }

    public function toArray(): array
    {
        return $this->jsonSerialize();
    }
}
