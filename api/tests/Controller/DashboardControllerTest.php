<?php

namespace App\Tests\Controller;

class DashboardControllerTest extends CommandTestBaseClass
{
    /**
     * @test
     * @throws \Exception
     */
    public function aUserCanReadHisPrivateTools()
    {
        $user = $this->createRandomUser();
        $privateTool = $this->createSimpleTool($user, false);

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendRequest('api/tools/' . $privateTool->tool(), $token);

        $this->assertEquals(200, $response->getStatusCode());
        $tools = json_decode($response->getContent(), true);
        $this->assertCount(1, $tools);
        $this->assertEquals($privateTool->toArray(), $tools[0]);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function aUserCanReadAllPublicTools()
    {
        $user1 = $this->createRandomUser();
        $publicTool1User1 = $this->createSimpleTool($user1, true);
        $this->createSimpleTool($user1, true);
        $this->createSimpleTool($user1, false);

        $user2 = $this->createRandomUser();
        $this->createSimpleTool($user2, true);
        $this->createSimpleTool($user2, true);
        $token = $this->getToken($user2->getUsername(), $user2->getPassword());

        $response = $this->sendRequest('api/tools/' . $publicTool1User1->tool().'/?public=true', $token);
        $this->assertEquals(200, $response->getStatusCode());
        $tools = json_decode($response->getContent(), true);
        $this->assertCount(4, $tools);
    }
}
