<?php

namespace App\Tests\Model;

use App\Model\Modflow\Discretization;
use PHPUnit\Framework\TestCase;

class DiscretizationTest extends TestCase
{

    private $geometry;
    private $boundingBox;
    private $gridSize;
    private $activeCells;
    private $stressperiods;
    private $timeUnit;
    private $lengthUnit;

    public function setUp()
    {
        $this->geometry = [
            'type' => 'Polygon',
            'coordinates' => [[5, 5], [5, 6], [5, 7], [5, 5]]
        ];

        $this->boundingBox = [
            [1, 1], [10, 10]
        ];

        $this->gridSize = [
            'n_x' => 10,
            'n_y' => 20
        ];

        $this->activeCells = [
            [5, 6],
            [5, 7],
            [5, 8],
            [5, 9],
            [6, 6],
            [6, 7],
            [6, 8],
        ];

        $this->stressperiods = [
            'start_date_time' => '2010-01-01',
            'end_date_time' => '2019-12-31',
            'time_unit' => 4,
            'stressperiods' => [
                ['totim_start' => 0, 'perlen' => 31, 'nstp' => 1, 'tsmult' => 1, 'steady' => true],
                ['totim_start' => 31, 'perlen' => 31, 'nstp' => 1, 'tsmult' => 1, 'steady' => false],
                ['totim_start' => 62, 'perlen' => 31, 'nstp' => 1, 'tsmult' => 1, 'steady' => false]
            ]
        ];

        $this->timeUnit = 4;
        $this->lengthUnit = 1;
    }

    /**
     * @test
     */
    public function can_be_instantiated_from_params(): void
    {
        $disc = Discretization::fromParams($this->geometry, $this->boundingBox, $this->gridSize, $this->activeCells, $this->stressperiods, $this->lengthUnit, $this->timeUnit);
        $this->assertInstanceOf(Discretization::class, $disc);
        $this->assertEquals($this->geometry, $disc->geometry());
        $this->assertEquals($this->boundingBox, $disc->boundingBox());
        $this->assertEquals($this->gridSize, $disc->gridSize());
        $this->assertEquals($this->activeCells, $disc->activeCells());
        $this->assertEquals($this->stressperiods, $disc->stressperiods());
        $this->assertEquals($this->timeUnit, $disc->timeUnit());
        $this->assertEquals($this->lengthUnit, $disc->lengthUnit());
    }

    /**
     * @test
     */
    public function can_be_instantiated_from_array_and_converted_to_array(): void
    {

        $arr = [
            'geometry' => $this->geometry,
            'bounding_box' => $this->boundingBox,
            'grid_size' => $this->gridSize,
            'active_cells' => $this->activeCells,
            'stressperiods' => $this->stressperiods,
            'length_unit' => $this->lengthUnit,
            'time_unit' => $this->timeUnit
        ];

        $disc = Discretization::fromArray($arr);
        $this->assertEquals($arr, $disc->toArray());
    }

    /**
     * @test
     */
    public function can_be_checked_if_equals_to_other_instance(): void
    {

        $arr1 = $arr2 = [
            'geometry' => $this->geometry,
            'bounding_box' => $this->boundingBox,
            'grid_size' => $this->gridSize,
            'active_cells' => $this->activeCells,
            'stressperiods' => $this->stressperiods,
            'length_unit' => $this->lengthUnit,
            'time_unit' => $this->timeUnit
        ];

        $disc = Discretization::fromArray($arr1);
        $this->assertTrue($disc->isEqualTo(Discretization::fromArray($arr2)));

        $arr2['time_unit'] = 3;
        $this->assertFalse($disc->isEqualTo(Discretization::fromArray($arr2)));

        $arr2 = $arr1;
        $arr2['stressperiods']['stressperiods']['totim_start'] = 1;
        $this->assertFalse($disc->isEqualTo(Discretization::fromArray($arr2)));
    }

    /**
     * @test
     */
    public function it_can_calculate_a_diff_to_another_instance(): void
    {
        $arr1 = $arr2 = [
            'geometry' => $this->geometry,
            'bounding_box' => $this->boundingBox,
            'grid_size' => $this->gridSize,
            'active_cells' => $this->activeCells,
            'stressperiods' => $this->stressperiods,
            'length_unit' => $this->lengthUnit,
            'time_unit' => $this->timeUnit
        ];

        $arr2['geometry']['coordinates'] = [[5, 5], [5, 6], [5, 7], [5, 6]];
        $this->assertEquals(['geometry' => ['coordinates' => ["3" => ["1" => 6]]]], Discretization::fromArray($arr1)->diff(Discretization::fromArray($arr2)));

        $arr2 = $arr1;
        $arr2['stressperiods']['stressperiods']['totim_start'] = 1;
        $this->assertEquals(['stressperiods' => ['stressperiods' => ['totim_start' => 1]]], Discretization::fromArray($arr1)->diff(Discretization::fromArray($arr2)));
    }

    /**
     * @test
     */
    public function it_can_merge_a_diff_and_create_a_new_instance(): void
    {
        $arr1 = $arr2 = [
            'geometry' => $this->geometry,
            'bounding_box' => $this->boundingBox,
            'grid_size' => $this->gridSize,
            'active_cells' => $this->activeCells,
            'stressperiods' => $this->stressperiods,
            'length_unit' => $this->lengthUnit,
            'time_unit' => $this->timeUnit
        ];

        $arr2['geometry']['coordinates'] = [[5, 5], [5, 6], [5, 7], [5, 6]];

        $disc = Discretization::fromArray($arr1);
        $diff = $disc->diff(Discretization::fromArray($arr2));
        $this->assertEquals($arr2, $disc->merge($diff)->toArray());
    }

    /**
     * @test
     */
    public function it_can_create_and_merge_a_shallow_diff(): void
    {
        $arr1 = $arr2 = [
            'geometry' => $this->geometry,
            'bounding_box' => $this->boundingBox,
            'grid_size' => $this->gridSize,
            'active_cells' => $this->activeCells,
            'stressperiods' => $this->stressperiods,
            'length_unit' => $this->lengthUnit,
            'time_unit' => $this->timeUnit
        ];

        $arr2['geometry']['coordinates'] = [[5, 5], [5, 6], [5, 7], [5, 6]];

        $expected = ['geometry' => $arr2['geometry']];
        $disc = Discretization::fromArray($arr1);
        $diff = $disc->array_shallow_diff(Discretization::fromArray($arr2));
        $this->assertEquals($expected, $diff);

        /** @var Discretization $disc **/
        $disc = $disc->array_merge_shallow_diff($diff);
        $this->assertEquals($arr2['geometry'], $disc->geometry());
    }
}
