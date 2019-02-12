<?php

namespace App\Tests\Model;

use App\Model\ToolMetadata;
use PHPUnit\Framework\TestCase;

class ToolMetadataTest extends TestCase
{
    /**
     * @test
     */
    public function can_be_instantiated_from_params(): void
    {
        $toolMetadata = ToolMetadata::fromParams('name', 'desc', false);
        $this->assertEquals('name', $toolMetadata->name());
        $this->assertEquals('desc', $toolMetadata->description());
        $this->assertFalse($toolMetadata->isPublic());
    }

    /**
     * @test
     */
    public function can_be_instantiated_from_array(): void
    {

        $arr = [
            'name' => 'name1',
            'description' => 'description1',
            'public' => false
        ];

        $toolMetadata = ToolMetadata::fromArray($arr);
        $this->assertEquals($arr['name'], $toolMetadata->name());
        $this->assertEquals($arr['description'], $toolMetadata->description());
        $this->assertEquals($arr['public'], $toolMetadata->isPublic());
    }

    /**
     * @test
     */
    public function can_be_converted_to_array(): void
    {

        $arr = [
            'name' => 'name1',
            'description' => 'description1',
            'public' => false
        ];

        $toolMetadata = ToolMetadata::fromArray($arr);
        $this->assertEquals($arr, $toolMetadata->toArray());
    }

    /**
     * @test
     */
    public function can_be_checked_if_equals_to_other_instance(): void
    {

        $arr1 = [
            'name' => 'name1',
            'description' => 'description1',
            'public' => false
        ];

        $arr2 = [
            'description' => 'description1',
            'public' => false,
            'name' => 'name1'
        ];

        $toolMetadata = ToolMetadata::fromArray($arr1);
        $this->assertTrue($toolMetadata->isEqualTo(ToolMetadata::fromArray($arr2)));

        $arr2 = [
            'description' => 'description2',
            'public' => false,
            'name' => 'name1'
        ];

        $this->assertFalse($toolMetadata->isEqualTo(ToolMetadata::fromArray($arr2)));
    }


    /**
     * @test
     */
    public function it_can_calculate_a_diff_to_another_instance(): void
    {
        $arr1 = [
            'name' => 'name1',
            'description' => 'description1',
            'public' => false
        ];

        $arr2 = [
            'name' => 'name2',
            'description' => 'description1',
            'public' => true,
        ];

        $toolMetadata = ToolMetadata::fromArray($arr1);
        $expected = ['name' => 'name2', 'public' => true];
        $this->assertEquals($expected, $toolMetadata->diff(ToolMetadata::fromArray($arr2)));
    }

    /**
     * @test
     */
    public function it_can_merge_a_diff_and_create_a_new_instance(): void
    {
        $arr1 = [
            'name' => 'name1',
            'description' => 'description1',
            'public' => false
        ];

        $arr2 = [
            'name' => 'name2',
            'description' => 'description1',
            'public' => true,
        ];

        $toolMetadata = ToolMetadata::fromArray($arr1);
        $diff = $toolMetadata->diff(ToolMetadata::fromArray($arr2));
        $this->assertEquals($arr2, $toolMetadata->merge($diff)->toArray());
    }
}
