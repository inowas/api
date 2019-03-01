<?php

declare(strict_types=1);

namespace App\Tests\Model\Boundary;

use App\Model\Modflow\Boundary\BoundaryFactory;
use App\Model\Modflow\Boundary\RechargeBoundary;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Swaggest\JsonSchema\Schema;

class RechargeBoundaryTest extends TestCase
{

    private $rechargeBoundaryJson;

    /**
     * @throws \Exception
     */
    public function setUp()
    {
        $this->rechargeBoundaryJson = [
            'id' => Uuid::uuid4()->toString(),
            'type' => "Feature",
            'geometry' => [
                'type' => 'Polygon',
                'coordinates' => [
                    [[125.6, 10.1], [125.7, 10.1], [125.7, 10.2], [125.6, 10.2], [125.6, 10.1]]
                ]
            ],
            'properties' => [
                'type' => 'rch',
                'name' => 'My new Recharge',
                'layers' => [1],
                'cells' => [[3, 4], [4, 5]],
                'sp_values' => [[0.0002], [0.0002], [0.0002], [0.0002]]
            ]
        ];
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_validates_the_recharge_boundary_schema_successfully()
    {
        $schema = 'https://schema.inowas.com/modflow/boundary/rechargeBoundary.json';
        $schema = Schema::import($schema);
        $object = json_decode(json_encode($this->rechargeBoundaryJson), false);
        $schema->in($object);
        $this->assertTrue(true);

        $wellBoundary = BoundaryFactory::fromArray($this->rechargeBoundaryJson);
        $object = json_decode(json_encode($wellBoundary->jsonSerialize()), false);
        $schema->in($object);
        $this->assertTrue(true);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_creates_a_recharge_boundary_from_json()
    {
        /** @var RechargeBoundary $rechargeBoundary */
        $rechargeBoundary = BoundaryFactory::fromArray($this->rechargeBoundaryJson);
        $this->assertInstanceOf(RechargeBoundary::class, $rechargeBoundary);
        $this->assertEquals($this->rechargeBoundaryJson['id'], $rechargeBoundary->getId());
        $this->assertEquals($this->rechargeBoundaryJson['type'], $rechargeBoundary->getType());

        $this->assertEquals($this->rechargeBoundaryJson['geometry']['type'], $rechargeBoundary->geometry()->getType());
        $this->assertEquals($this->rechargeBoundaryJson['geometry']['coordinates'], $rechargeBoundary->geometry()->getCoordinates());
        $this->assertEquals($this->rechargeBoundaryJson['properties'], $rechargeBoundary->getProperties());
        $this->assertEquals($this->rechargeBoundaryJson['properties']['type'], $rechargeBoundary->type());
        $this->assertEquals($this->rechargeBoundaryJson['properties']['layers'], $rechargeBoundary->layers());
        $this->assertEquals($this->rechargeBoundaryJson['properties']['cells'], $rechargeBoundary->cells());
        $this->assertEquals($this->rechargeBoundaryJson['properties']['sp_values'], $rechargeBoundary->spValues());
        $this->assertEquals($this->rechargeBoundaryJson, $rechargeBoundary->jsonSerialize());
    }
}
