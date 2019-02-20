<?php

namespace App\Tests\Controller;

class UserAuthenticationTest extends CommandTestBaseClass
{


    public function provider()
    {
        return [
            ['admin', 'admin_pw', ['ROLE_ADMIN'], 200],
            ['user', 'user_pw', ['ROLE_USER'], 403]
        ];
    }

    /**
     * @dataProvider provider
     * @param $username
     * @param $password
     * @param $roles
     * @param $statusCode
     * @throws \Exception
     */
    public function testAuthentication($username, $password, $roles, $statusCode)
    {
        $client = static::createClient();
        $this->createUser($username, $password, $roles);
        $token = $this->getToken($username, $password);

        $client->request(
            'GET',
            '/api/users.json',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => sprintf('Bearer %s',  $token)
            ]
        );

        $this->assertEquals($statusCode, $client->getResponse()->getStatusCode());
    }
}
