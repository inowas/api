<?php

declare(strict_types=1);

namespace App\Tests\Model\Boundary;

use App\Model\Modflow\Boundary\BoundaryFactory;
use App\Model\Modflow\Boundary\ConstantHeadBoundary;
use GeoJson\Feature\Feature;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Swaggest\JsonSchema\Schema;

class ConstantHeadBoundaryTest extends TestCase
{

    private $constantHeadBoundaryJson;

    /**
     * @throws \Exception
     */
    public function setUp()
    {
        $this->constantHeadBoundaryJson = [
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
                        'type' => 'chd',
                        'name' => 'My new chd-boundary',
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
    public function it_validates_the_constant_head_boundary_schema_successfully()
    {
        $schema = 'https://schema.inowas.com/modflow/boundary/constantHeadBoundary.json';
        $schema = Schema::import($schema);
        $object = json_decode(json_encode($this->constantHeadBoundaryJson), false);
        $schema->in($object);
        $this->assertTrue(true);

        $constantHeadBoundary = BoundaryFactory::fromArray($this->constantHeadBoundaryJson);
        $object = json_decode(json_encode($constantHeadBoundary->jsonSerialize()), false);
        $schema->in($object);
        $this->assertTrue(true);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_creates_a_constant_head_boundary_from_json()
    {
        /** @var ConstantHeadBoundary $constantHeadBoundary */
        $constantHeadBoundary = BoundaryFactory::fromArray($this->constantHeadBoundaryJson);
        $this->assertInstanceOf(ConstantHeadBoundary::class, $constantHeadBoundary);
        $this->assertInstanceOf(Feature::class, $constantHeadBoundary->constantHeadBoundary());
        $this->assertCount(1, $constantHeadBoundary->observationPoints());
    }
}
