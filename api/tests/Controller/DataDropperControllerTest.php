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
        $content = json_encode($privateTool->toArray());

        $base64Content = base64_encode($content);

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendPostRequest('v3/datadropper', $base64Content, $token);
        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getContent(), true);
        $filename = $response['filename'];

        $response = $this->sendRequest('v3/datadropper/' . $filename, $token);
        $this->assertEquals(200, $response->getStatusCode());

        $base64Response = $response->getContent();
        $this->assertEquals($content, base64_decode($base64Response));
    }
}
