<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SchemaControllerTest extends WebTestCase
{

    /**
     * @test
     */
    public function getSchemaFolder()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/schema'
        );

        $this->assertContains('<a href="/schema/commands">commands</a>', $client->getResponse()->getContent());
        $this->assertContains('<a href="/schema/geojson">geojson</a>', $client->getResponse()->getContent());
        $this->assertContains('<a href="/schema/modflow">modflow</a>', $client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    /**
     * @test
     */
    public function getSchema()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            'schema/commands/addBoundary.json',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(file_get_contents(__DIR__.'/../../schema/commands/addBoundary.json'), $client->getResponse()->getContent());
    }
}
