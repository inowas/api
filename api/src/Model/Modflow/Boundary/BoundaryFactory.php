<?php

namespace App\Model\Modflow\Boundary;

use Assert\Assertion;
use GeoJson\Feature\Feature;
use GeoJson\Feature\FeatureCollection;
use GeoJson\GeoJson;

class BoundaryFactory
{
    /** @var string */
    protected $id;

    protected $data;

    /**
     * @param array $arr
     * @return RiverBoundary|WellBoundary
     */
    public static function fromArray(array $arr): ?BoundaryInterface
    {

        $geoJson = GeoJson::jsonUnserialize($arr);

        if ($geoJson instanceof Feature) {
            Assertion::keyExists($geoJson->getProperties(), 'type');
            $type = $geoJson->getProperties()['type'];

            switch ($type) {
                case 'hob':
                    return HeadObservationWell::fromArray($arr);
                    break;
                case 'evt':
                    return EvapotranspirationBoundary::fromArray($arr);
                    break;
                case 'rch':
                    return RechargeBoundary::fromArray($arr);
                    break;
                case 'wel':
                    return WellBoundary::fromArray($arr);
                    break;
                default:
                    return null;
            }
        }

        if ($geoJson instanceof FeatureCollection) {
            /** @var Feature $feature */
            foreach ($geoJson->getFeatures() as $feature) {
                Assertion::keyExists($feature->getProperties(), 'type');
                $type = $feature->getProperties()['type'];

                switch ($type) {
                    case 'chd':
                        return ConstantHeadBoundary::fromArray($arr);
                        break;
                    case 'fhb':
                        return FlowAndHeadBoundary::fromArray($arr);
                        break;
                    case 'drn':
                        return DrainageBoundary::fromArray($arr);
                        break;
                    case 'ghb':
                        return GeneralHeadBoundary::fromArray($arr);
                        break;
                    case 'riv':
                        return RiverBoundary::fromArray($arr);
                        break;
                }
            }
        }

        return null;
    }
}
