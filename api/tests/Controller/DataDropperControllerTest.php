<?php

namespace App\Tests\Controller;

class DataDropperControllerTest extends CommandTestBaseClass
{
    /**
     * @test
     * @throws \Exception
     */
    public function aUserCanDropData(): void
    {
        $user = $this->createRandomUser();
        $privateTool = $this->createSimpleTool($user, false);
        $content = json_encode($privateTool->toArray(), JSON_UNESCAPED_UNICODE);

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendPostRequest('v3/datadropper', $content, $token);
        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getContent(), true);
        $filename = $response['filename'];

        $response = $this->sendRequest('v3/datadropper/' . $filename, $token);
        $this->assertEquals(200, $response->getStatusCode());

        var_dump($response->getContent());

        $this->assertEquals($content, $response->getContent());
    }
}
