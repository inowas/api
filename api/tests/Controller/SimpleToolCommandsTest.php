<?php

namespace App\Tests\Controller;

use App\Model\SimpleTool\SimpleTool;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class SimpleToolCommandsTest extends CommandTestBaseClass
{
    /**
     * @test
     * @throws \Exception
     */
    public function sendCreateSimpleToolCommand()
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
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'createToolInstance',
            'metadata' => (object)[],
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

        return ['user' => $user, 'command' => $command];
    }

    /**
     * @test
     * @depends sendCreateSimpleToolCommand
     * @param array $data
     * @throws \Exception
     */
    public function simpleToolWasStoredCorrectly(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];
        $command = $data['command'];
        $toolInstanceId = $command['payload']['id'];

        /** @var SimpleTool $simpleTool */
        $simpleTool = self::$container->get('doctrine')->getRepository(SimpleTool::class)->findOneById($toolInstanceId);
        $this->assertEquals($command['payload']['tool'], $simpleTool->tool());
        $this->assertEquals($command['payload']['name'], $simpleTool->name());
        $this->assertEquals($command['payload']['description'], $simpleTool->description());
        $this->assertEquals($command['payload']['public'], $simpleTool->isPublic());
        $this->assertEquals($command['payload']['data'], $simpleTool->data());
        $this->assertEquals($user->getId()->toString(), $simpleTool->userId());
    }


    /**
     * @test
     * @depends sendCreateSimpleToolCommand
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function sendCloneToolInstanceCommand(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];

        $command = $data['command'];
        $toolInstanceId = $command['payload']['id'];

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'cloneToolInstance',
            'metadata' => (object)[],
            'payload' => [
                'id' => Uuid::uuid4()->toString(),
                'base_id' => $toolInstanceId
            ]
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        return ['user' => $user, 'command' => $command];
    }

    /**
     * @test
     * @depends sendCloneToolInstanceCommand
     * @param array $data
     * @throws \Exception
     */
    public function simpleToolWasClonedCorrectly(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];
        $command = $data['command'];
        $newId = $command['payload']['id'];
        $baseId = $command['payload']['base_id'];

        $simpleTools = self::$container->get('doctrine')->getRepository(SimpleTool::class)->findAll();
        $this->assertCount(2, $simpleTools);

        /** @var SimpleTool $oldSimpleTool */
        $oldSimpleTool = self::$container->get('doctrine')->getRepository(SimpleTool::class)->findOneById($baseId);

        /** @var SimpleTool $simpleTool */
        $simpleTool = self::$container->get('doctrine')->getRepository(SimpleTool::class)->findOneById($newId);

        $this->assertEquals($oldSimpleTool->tool(), $simpleTool->tool());
        $this->assertEquals($oldSimpleTool->name(), $simpleTool->name());
        $this->assertEquals($oldSimpleTool->description(), $simpleTool->description());
        $this->assertEquals($oldSimpleTool->isPublic(), $simpleTool->isPublic());
        $this->assertEquals($oldSimpleTool->data(), $simpleTool->data());
        $this->assertEquals($user->getId()->toString(), $simpleTool->userId());
    }

    /**
     * @test
     * @depends sendCreateSimpleToolCommand
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function sendUpdateSimpleToolCommand(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];

        $command = $data['command'];
        $toolInstanceId = $command['payload']['id'];

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'updateToolInstance',
            'metadata' => (object)[],
            'payload' => [
                'id' => $toolInstanceId,
                'name' => 'ToolNewName',
                'description' => 'ToolNewDescription',
                'public' => true,
                'data' => ['a' => 'very', 'complex' => 'dataset']
            ]
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        return ['user' => $user, 'command' => $command];
    }

    /**
     * @test
     * @depends sendUpdateSimpleToolCommand
     * @param array $data
     * @throws \Exception
     */
    public function simpleToolWasUpdatedCorrectly(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];
        $command = $data['command'];
        $toolInstanceId = $command['payload']['id'];

        /** @var SimpleTool $simpleTool */
        $simpleTool = self::$container->get('doctrine')->getRepository(SimpleTool::class)->findOneById($toolInstanceId);
        $this->assertEquals($command['payload']['name'], $simpleTool->name());
        $this->assertEquals($command['payload']['description'], $simpleTool->description());
        $this->assertEquals($command['payload']['public'], $simpleTool->isPublic());
        $this->assertEquals($command['payload']['data'], $simpleTool->data());
        $this->assertEquals($user->getId()->toString(), $simpleTool->userId());
    }

    /**
     * @test
     * @depends sendCreateSimpleToolCommand
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function sendUpdateSimpleToolMetadataCommand(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];

        $command = $data['command'];
        $toolInstanceId = $command['payload']['id'];

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'updateToolInstanceMetadata',
            'metadata' => (object)[],
            'payload' => [
                'id' => $toolInstanceId,
                'name' => 'ToolNewNameUpdated',
                'description' => 'ToolNewDescriptionUpdated',
                'public' => true
            ]
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        return ['user' => $user, 'command' => $command];
    }

    /**
     * @test
     * @depends sendUpdateSimpleToolMetadataCommand
     * @param array $data
     * @throws \Exception
     */
    public function sendUpdateSimpleToolMetadataWasUpdatedCorrectly(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];
        $command = $data['command'];
        $toolInstanceId = $command['payload']['id'];

        /** @var SimpleTool $simpleTool */
        $simpleTool = self::$container->get('doctrine')->getRepository(SimpleTool::class)->findOneById($toolInstanceId);
        $this->assertEquals($command['payload']['name'], $simpleTool->name());
        $this->assertEquals($command['payload']['description'], $simpleTool->description());
        $this->assertEquals($command['payload']['public'], $simpleTool->isPublic());
        $this->assertEquals($user->getId()->toString(), $simpleTool->userId());
    }

    /**
     * @test
     * @depends sendCreateSimpleToolCommand
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function sendUpdateSimpleToolDataCommand(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];

        $command = $data['command'];
        $toolInstanceId = $command['payload']['id'];

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'updateToolInstanceData',
            'metadata' => (object)[],
            'payload' => [
                'id' => $toolInstanceId,
                'data' => ['a' => 'very', 'complex' => 'dataset', 'update' => 'now']
            ]
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        return ['user' => $user, 'command' => $command];
    }

    /**
     * @test
     * @depends sendUpdateSimpleToolDataCommand
     * @param array $data
     * @throws \Exception
     */
    public function simpleToolDataWasUpdatedCorrectly(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];
        $command = $data['command'];
        $toolInstanceId = $command['payload']['id'];

        /** @var SimpleTool $simpleTool */
        $simpleTool = self::$container->get('doctrine')->getRepository(SimpleTool::class)->findOneById($toolInstanceId);
        $this->assertEquals($command['payload']['data'], $simpleTool->data());
        $this->assertEquals($user->getId()->toString(), $simpleTool->userId());
    }

    /**
     * @test
     * @depends sendCreateSimpleToolCommand
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function sendDeleteToolInstanceCommand(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];

        $command = $data['command'];
        $toolInstanceId = $command['payload']['id'];

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'deleteToolInstance',
            'metadata' => (object)[],
            'payload' => [
                'id' => $toolInstanceId
            ]
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        return ['user' => $user, 'command' => $command];
    }

    /**
     * @test
     * @depends sendDeleteToolInstanceCommand
     * @param array $data
     * @throws \Exception
     */
    public function simpleToolWasDeletedCorrectly(array $data)
    {
        static::createClient();

        $command = $data['command'];
        $toolInstanceId = $command['payload']['id'];

        /** @var SimpleTool $simpleTool */
        $simpleTool = self::$container->get('doctrine')->getRepository(SimpleTool::class)->findOneById($toolInstanceId);
        $this->assertTrue($simpleTool->isArchived());
    }
}
