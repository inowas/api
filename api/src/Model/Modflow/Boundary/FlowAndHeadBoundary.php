<?php

declare(strict_types=1);

namespace App\Model\Modflow\Boundary;

use GeoJson\Feature\Feature;
use GeoJson\Feature\FeatureCollection;
use GeoJson\GeoJson;
use GeoJson\Geometry\Geometry;
use RuntimeException;

final class FlowAndHeadBoundary extends FeatureCollection implements BoundaryInterface
{

    public const TYPE = 'fhb';

    /** @var Feature */
    private $flowAndHeadBoundary;

    /** @var array $observationPoints */
    private $observationPoints = [];

    /**
     * @param array $arr
     * @return self
     */
    public static function fromArray(array $arr): self
    {
        /** @var FeatureCollection $feature */
        $featureCollection = GeoJson::jsonUnserialize($arr);
        return new self($featureCollection->getFeatures());
    }

    /**
     * RiverBoundary constructor.
     * @param $features
     */
    public function __construct($features)
    {
        parent::__construct($features);

        /** @var Feature $feature */
        foreach ($features as $feature) {
            if ($feature->getProperties()['type'] === 'fhb') {
                $this->flowAndHeadBoundary = $feature;
            }

            if ($feature->getProperties()['type'] === 'op') {
                $this->observationPoints[] = $feature;
            }
        }

        if (null === $this->flowAndHeadBoundary) {
            throw new RuntimeException('One Feature has to contain a property from type "fhb"');
        }
    }

    public function id(): string
    {
        return $this->flowAndHeadBoundary->getId();
    }

    public function flowAndHeadBoundary(): Feature
    {
        return $this->flowAndHeadBoundary;
    }

    public function name(): string
    {
        return $this->flowAndHeadBoundary->getProperties()['name'];
    }

    public function geometry(): Geometry
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->flowAndHeadBoundary->getGeometry();
    }

    public function observationPoints(): array
    {
        return $this->observationPoints;
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
