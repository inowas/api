<?php

declare(strict_types=1);

namespace App\Model\Modflow\Boundary;

use GeoJson\Feature\Feature;
use GeoJson\Feature\FeatureCollection;
use GeoJson\GeoJson;
use GeoJson\Geometry\Geometry;

final class GeneralHeadBoundary extends FeatureCollection implements BoundaryInterface
{

    const TYPE = 'ghb';

    /** @var Feature */
    private $generalHeadBoundary;

    /** @var array $observationPoints */
    private $observationPoints = [];

    /**
     * @param array $arr
     * @return self
     * @throws \Exception
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
     * @throws \Exception
     */
    public function __construct($features)
    {
        parent::__construct($features);

        /** @var Feature $feature */
        foreach ($features as $feature) {
            if ($feature->getProperties()['type'] === 'ghb') {
                $this->generalHeadBoundary = $feature;
            }

            if ($feature->getProperties()['type'] === 'op') {
                $this->observationPoints[] = $feature;
            }
        }

        if (null === $this->generalHeadBoundary) {
            throw new \Exception('One Feature has to contain a property from type "chd"');
        }
    }

    public function id(): string
    {
        return $this->generalHeadBoundary->getId();
    }

    public function generalHeadBoundary(): Feature
    {
        return $this->generalHeadBoundary;
    }

    public function name(): string
    {
        return $this->generalHeadBoundary->getProperties()['name'];
    }

    public function geometry(): Geometry
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->generalHeadBoundary->getGeometry();
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
