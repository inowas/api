<?php

namespace App\Tests\Controller;

use App\Model\ToolInstance;
use App\Model\User;
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

        return ['username' => $username, 'password' => $password, 'toolInstance' => $toolInstance];
    }

    /**
     * @test
     * @depends aToolCanBeCreated
     * @param array $credentials
     * @return array
     * @throws \Exception
     */
    public function aToolCanBeCloned(array $credentials)
    {
        /** @var ToolInstance $oldToolInstance */
        $oldToolInstance = $credentials['toolInstance'];
        $username = $credentials['username'];
        $password = $credentials['password'];
        $newId = Uuid::uuid4()->toString();

        static::createClient();
        /** @var User $user */
        $user = self::$container->get('doctrine')->getRepository(User::class)->findOneByUsername($username);

        $command = [
            'message_name' => 'cloneToolInstance',
            'payload' => [
                'id' => $newId,
                'base_id' => $oldToolInstance->getId()
            ]
        ];

        $token = $this->getToken($username, $password);
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        $toolInstances = self::$container->get('doctrine')->getRepository(ToolInstance::class)->findAll();
        $this->assertCount(2, $toolInstances);

        /** @var ToolInstance $toolInstance */
        $toolInstance = self::$container->get('doctrine')->getRepository(ToolInstance::class)->findOneById($newId);

        $this->assertEquals($oldToolInstance->getTool(), $toolInstance->getTool());
        $this->assertEquals($oldToolInstance->getName(), $toolInstance->getName());
        $this->assertEquals($oldToolInstance->getDescription(), $toolInstance->getDescription());
        $this->assertEquals($oldToolInstance->isPublic(), $toolInstance->isPublic());
        $this->assertEquals($oldToolInstance->getData(), $toolInstance->getData());
        $this->assertEquals($user->getId()->toString(), $toolInstance->getUserId());
        $this->assertEquals($user->getUsername(), $toolInstance->getUsername());

        return ['username' => $username, 'password' => $password, 'toolInstance' => $toolInstance];
    }

    /**
     * @test
     * @depends aToolCanBeCreated
     * @param array $credentials
     * @throws \Exception
     */
    public function aToolCanBeUpdated(array $credentials)
    {
        /** @var ToolInstance $toolInstance */
        $toolInstance = $credentials['toolInstance'];
        $username = $credentials['username'];
        $password = $credentials['password'];

        static::createClient();
        /** @var User $user */
        $user = self::$container->get('doctrine')->getRepository(User::class)->findOneByUsername($username);

        $command = [
            'message_name' => 'updateToolInstance',
            'payload' => [
                'id' => $toolInstance->getId(),
                'name' => 'ToolNewName',
                'description' => 'ToolNewDescription',
                'public' => true,
                'data' => ['a' => 'very', 'complex' => 'dataset']
            ]
        ];

        $token = $this->getToken($username, $password);
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        $toolInstances = self::$container->get('doctrine')->getRepository(ToolInstance::class)->findAll();
        $this->assertCount(2, $toolInstances);

        /** @var ToolInstance $toolInstance */
        $toolInstance = self::$container->get('doctrine')->getRepository(ToolInstance::class)->findOneById($toolInstance->getId());
        $this->assertEquals($command['payload']['name'], $toolInstance->getName());
        $this->assertEquals($command['payload']['description'], $toolInstance->getDescription());
        $this->assertEquals($command['payload']['public'], $toolInstance->isPublic());
        $this->assertEquals($command['payload']['data'], $toolInstance->getData());
        $this->assertEquals($user->getId()->toString(), $toolInstance->getUserId());
        $this->assertEquals($user->getUsername(), $toolInstance->getUsername());
    }

    /**
     * @test
     * @depends aToolCanBeCreated
     * @param array $credentials
     * @throws \Exception
     */
    public function aToolCanBeDeleted(array $credentials)
    {
        /** @var ToolInstance $toolInstance */
        $toolInstance = $credentials['toolInstance'];
        $username = $credentials['username'];
        $password = $credentials['password'];

        static::createClient();
        $command = [
            'message_name' => 'deleteToolInstance',
            'payload' => [
                'id' => $toolInstance->getId(),
            ]
        ];

        $token = $this->getToken($username, $password);
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var ToolInstance $toolInstance */
        $toolInstance = self::$container->get('doctrine')->getRepository(ToolInstance::class)->findOneById($toolInstance->getId());
        $this->assertNull($toolInstance);
    }
}
