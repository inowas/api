<?php

declare(strict_types=1);

namespace App\Tests\Model\Boundary;

use App\Model\Modflow\Boundary\BoundaryFactory;
use App\Model\Modflow\Boundary\RiverBoundary;
use GeoJson\Feature\Feature;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class RiverBoundaryTest extends TestCase
{

    private $riverBoundaryJson;

    /**
     * @throws \Exception
     */
    public function setUp()
    {
        $this->riverBoundaryJson = [
            'type' => "FeatureCollection",
            'features' => [
                [
                    'type' => 'Feature',
                    'id' => Uuid::uuid4()->toString(),
                    'geometry' => [
                        'type' => 'LineString',
                        'coordinates' => [[125.6, 10.1], [125.7, 10.2], [125.8, 10.3]]
                    ],
                    'properties' => [
                        'type' => 'riv',
                        'name' => 'My new River',
                        'layers' => [1],
                        'cells' => [[3, 4], [4, 5]],
                    ]
                ],
                [
                    'type' => 'Feature',
                    'id' => Uuid::uuid4()->toString(),
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [125.6, 10.1]
                    ],
                    'properties' => [
                        'type' => 'op',
                        'name' => 'OP1',
                        'sp_values' => [1, 2, 3, 4, 5, 6, 7, 8]
                    ]
                ]
            ]
        ];
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_creates_a_river_from_json()
    {
        /** @var RiverBoundary $riverBoundary */
        $riverBoundary = BoundaryFactory::fromArray($this->riverBoundaryJson);
        $this->assertInstanceOf(RiverBoundary::class, $riverBoundary);
        $this->assertInstanceOf(Feature::class, $riverBoundary->river());
        $this->assertCount(1, $riverBoundary->observationPoints());
    }
}
