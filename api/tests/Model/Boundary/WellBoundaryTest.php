<?php

declare(strict_types=1);

namespace App\Tests\Model\Boundary;

use App\Model\Modflow\Boundary\BoundaryFactory;
use App\Model\Modflow\Boundary\WellBoundary;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Swaggest\JsonSchema\Schema;

class WellBoundaryTest extends TestCase
{

    private $wellBoundaryJson;

    /**
     * @throws \Exception
     */
    public function setUp()
    {
        $this->wellBoundaryJson = [
            'id' => Uuid::uuid4()->toString(),
            'type' => "Feature",
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [125.6, 10.1]
            ],
            'properties' => [
                'type' => 'wel',
                'name' => 'My new Well',
                'well_type' => 'puw',
                'layers' => [1],
                'cells' => [[3, 4], [4, 5]],
                'sp_values' => [3444, 5555, 666, 777]
            ]
        ];
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_validates_the_well_boundary_schema_successfully()
    {
        $schema = 'https://schema.inowas.com/modflow/boundary/wellBoundary.json';
        $schema = Schema::import($schema);
        $object = json_decode(json_encode($this->wellBoundaryJson), false);
        $schema->in($object);
        $this->assertTrue(true);

        $wellBoundary = BoundaryFactory::fromArray($this->wellBoundaryJson);
        $object = json_decode(json_encode($wellBoundary->jsonSerialize()), false);
        $schema->in($object);
        $this->assertTrue(true);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_creates_a_well_from_json()
    {
        /** @var WellBoundary $wellBoundary */
        $wellBoundary = BoundaryFactory::fromArray($this->wellBoundaryJson);
        $this->assertInstanceOf(WellBoundary::class, $wellBoundary);
        $this->assertEquals($this->wellBoundaryJson['id'], $wellBoundary->getId());
        $this->assertEquals($this->wellBoundaryJson['type'], $wellBoundary->getType());

        $this->assertEquals($this->wellBoundaryJson['geometry']['type'], $wellBoundary->geometry()->getType());
        $this->assertEquals($this->wellBoundaryJson['geometry']['coordinates'], $wellBoundary->geometry()->getCoordinates());
        $this->assertEquals($this->wellBoundaryJson['properties'], $wellBoundary->getProperties());
        $this->assertEquals($this->wellBoundaryJson['properties']['type'], $wellBoundary->type());
        $this->assertEquals($this->wellBoundaryJson['properties']['well_type'], $wellBoundary->wellType());
        $this->assertEquals($this->wellBoundaryJson['properties']['layers'], $wellBoundary->layers());
        $this->assertEquals($this->wellBoundaryJson['properties']['cells'], $wellBoundary->cells());
        $this->assertEquals($this->wellBoundaryJson['properties']['sp_values'], $wellBoundary->spValues());
        $this->assertEquals($this->wellBoundaryJson, $wellBoundary->jsonSerialize());
    }

}
