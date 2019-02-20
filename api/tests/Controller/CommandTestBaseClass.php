<?php

namespace App\Tests\Controller;

use App\Model\SimpleTool\SimpleTool;
use App\Model\ToolMetadata;
use App\Model\User;
use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommandTestBaseClass extends WebTestCase
{
    /**
     * @throws \Exception
     */
    protected function createRandomUser(): User
    {
        static::createClient();
        $username = sprintf('newUser_%d', rand(1000000, 10000000 - 1));
        $password = sprintf('newUserPassword_%d', rand(1000000, 10000000 - 1));
        $user = new User($username, $password, ['ROLE_USER']);

        /** @var EntityManager $em */
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush($user);

        return $user;
    }

    /**
     * @param User $user
     * @param bool $isPublic
     * @return SimpleTool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    protected function createSimpleTool(User $user, bool $isPublic = true): SimpleTool
    {
        $id = Uuid::uuid4()->toString();
        $simpleTool = SimpleTool::createWithParams($id, $user, 'T02', ToolMetadata::fromParams(
            'Tool01_' . rand(10000, 99999),
            'Description_' . rand(10000, 99999),
            $isPublic
        ));

        $simpleTool->setData(['123' => 123]);

        /** @var EntityManager $em */
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($simpleTool);
        $em->flush();

        return $simpleTool;
    }

    /**
     * @param $username
     * @param $password
     * @param array $roles
     * @return User
     * @throws \Exception
     */
    protected function createUser($username, $password, $roles = ['ROLE_USER']): User
    {
        $user = new User($username, $password, $roles);
        /** @var EntityManager $em */
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush($user);

        return $user;
    }

    /**
     * @param $username
     * @param $password
     * @return string
     */
    protected function getToken($username, $password): string
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(["username" => $username, "password" => $password])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        return $content['token'];
    }

    /**
     * @param $endpoint
     * @param $command
     * @param null $token
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendCommand($endpoint, $command, $token = null)
    {
        $headers = $token ? ['HTTP_Authorization' => sprintf('Bearer %s', $token)] : [];
        $headers['CONTENT_TYPE'] = 'application/json';
        $client = static::createClient();
        $client->request(
            'POST',
            $endpoint,
            [],
            [],
            $headers,
            json_encode($command)
        );

        return $client->getResponse();
    }

    /**
     * @param $endpoint
     * @param null $token
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendRequest($endpoint, $token = null)
    {
        $headers = $token ? ['HTTP_Authorization' => sprintf('Bearer %s', $token)] : [];
        $headers['CONTENT_TYPE'] = 'application/json';
        $client = static::createClient();
        $client->request(
            'GET',
            $endpoint,
            [],
            [],
            $headers
        );

        return $client->getResponse();
    }
}
