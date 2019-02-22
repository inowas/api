<?php

declare(strict_types=1);

namespace App\Tests\Model\Boundary;

use App\Model\Modflow\Boundary\BoundaryFactory;
use App\Model\Modflow\Boundary\GeneralHeadBoundary;
use GeoJson\Feature\Feature;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Swaggest\JsonSchema\Schema;

class GeneralHeadBoundaryTest extends TestCase
{

    private $generalHeadHeadBoundaryJson;

    /**
     * @throws \Exception
     */
    public function setUp()
    {
        $this->generalHeadHeadBoundaryJson = [
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
                        'type' => 'ghb',
                        'name' => 'My new ghb-boundary',
                        'layers' => [1],
                        'cells' => [[3, 4], [4, 5]],
                    ]
                ],
                [
                    'type' => 'Feature',
                    'id' => 'op1',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [125.6, 10.1]
                    ],
                    'properties' => [
                        'type' => 'op',
                        'name' => 'OP1',
                        'sp_values' => [
                            [1, 2]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_validates_the_general_head_boundary_schema_successfully()
    {
        $schema = 'https://schema.inowas.com/modflow/boundary/generalHeadBoundary.json';
        $schema = Schema::import($schema);
        $object = json_decode(json_encode($this->generalHeadHeadBoundaryJson), false);
        $schema->in($object);
        $this->assertTrue(true);

        $generalHeadBoundary = BoundaryFactory::fromArray($this->generalHeadHeadBoundaryJson);
        $object = json_decode(json_encode($generalHeadBoundary->jsonSerialize()), false);
        $schema->in($object);
        $this->assertTrue(true);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_creates_a_general_head_boundary_from_json()
    {
        /** @var GeneralHeadBoundary $generalHeadBoundary */
        $generalHeadBoundary = BoundaryFactory::fromArray($this->generalHeadHeadBoundaryJson);
        $this->assertInstanceOf(GeneralHeadBoundary::class, $generalHeadBoundary);
        $this->assertInstanceOf(Feature::class, $generalHeadBoundary->generalHeadBoundary());
        $this->assertCount(1, $generalHeadBoundary->observationPoints());
        $this->assertEquals($this->generalHeadHeadBoundaryJson, $generalHeadBoundary->jsonSerialize());
    }
}
