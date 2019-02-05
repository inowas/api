<?php

namespace App\Tests\Controller;

use App\Entity\ToolInstance;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class ToolInstanceCommandsTest extends CommandTestBaseClass
{


    /**
     * @test
     * @throws \Exception
     */
    public function aToolCanBeCreated()
    {
        static::createClient();

        $username = sprintf('newUser_%d', rand(1000000, 10000000 - 1));
        $password = sprintf('newUserPassword_%d', rand(1000000, 10000000 - 1));

        $user = new User($username, $password, ['ROLE_USER']);

        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        $toolInstanceId = Uuid::uuid4()->toString();

        $command = [
            'message_name' => 'createToolInstance',
            'payload' => [
                'id' => $toolInstanceId,
                'tool' => 'T0TEST',
                'name' => 'ToolName',
                'description' => 'ToolDescription',
                'public' => false,
                'data' => ['1234' => '5678']
            ]
        ];

        $token = $this->getToken($username, $password);
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var ToolInstance $toolInstance */
        $toolInstance = self::$container->get('doctrine')->getRepository(ToolInstance::class)->findOneById($toolInstanceId);
        $this->assertEquals($command['payload']['tool'], $toolInstance->getTool());
        $this->assertEquals($command['payload']['name'], $toolInstance->getName());
        $this->assertEquals($command['payload']['description'], $toolInstance->getDescription());
        $this->assertEquals($command['payload']['public'], $toolInstance->isPublic());
        $this->assertEquals($command['payload']['data'], $toolInstance->getData());
        $this->assertEquals($user->getId()->toString(), $toolInstance->getUserId());
        $this->assertEquals($user->getUsername(), $toolInstance->getUsername());

        return ['username' => $username, 'password' => $password];
    }
}
