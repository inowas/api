<?php

namespace App\Model\Modflow\Boundary;

use GeoJson\Feature\Feature;
use GeoJson\Geometry\Geometry;
use RuntimeException;

final class EvapotranspirationBoundary extends Feature implements BoundaryInterface
{

    public const TYPE = 'evt';

    /**
     * @param array $arr
     * @return EvapotranspirationBoundary
     */
    public static function fromArray(array $arr): self
    {
        /** @var Feature $feature */
        $self = self::jsonUnserialize($arr);

        if (!$self instanceof Feature) {
            throw new RuntimeException('Invalid json, expecting type feature.');
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

    public function type(): string
    {
        return self::TYPE;
    }

    public function toArray(): array
    {
        return $this->jsonSerialize();
    }
}
