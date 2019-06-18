<?php

declare(strict_types=1);

namespace App\Model\Modflow\Boundary;

use GeoJson\Feature\Feature;
use GeoJson\Feature\FeatureCollection;
use GeoJson\GeoJson;
use GeoJson\Geometry\Geometry;
use RuntimeException;

final class ConstantHeadBoundary extends FeatureCollection implements BoundaryInterface
{

    public const TYPE = 'chd';

    /** @var Feature */
    private $constantHeadBoundary;

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
            if ($feature->getProperties()['type'] === 'chd') {
                $this->constantHeadBoundary = $feature;
            }

            if ($feature->getProperties()['type'] === 'op') {
                $this->observationPoints[] = $feature;
            }
        }

        if (null === $this->constantHeadBoundary) {
            throw new RuntimeException('One Feature has to contain a property from type "chd"');
        }
    }

    public function id(): string
    {
        return $this->constantHeadBoundary->getId();
    }

    public function constantHeadBoundary(): Feature
    {
        return $this->constantHeadBoundary;
    }

    public function name(): string
    {
        return $this->constantHeadBoundary->getProperties()['name'];
    }

    public function geometry(): Geometry
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->constantHeadBoundary->getGeometry();
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
