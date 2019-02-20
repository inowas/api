<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessageBoxControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function sendCommandWithoutTokenReturns401()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/v3/messagebox',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([])
        );

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * test
     */
    public function sendInvalidCommandByValidUser()
    {
        $command = [
            'message_name' => 'testMessage'
        ];

        $response = $this->sendCommand('admin', 'admin_pw', $command);
        $this->assertEquals(322, $response->getStatusCode());
    }

    /**
     * @param $username
     * @param $password
     * @param $command
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendCommand($username, $password, $command)
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/v3/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(["username" => $username, "password" => $password])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        $token = $content['token'];

        $client->request(
            'GET',
            '/v3/users',
            [],
            [],
            ['HTTP_Authorization' => sprintf('Bearer %s',  $token)],
            json_encode($command)
        );

        return $client->getResponse();
    }
}
