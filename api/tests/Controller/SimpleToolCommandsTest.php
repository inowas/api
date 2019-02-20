<?php

namespace App\Tests\Controller;

use App\Model\SimpleTool\SimpleTool;
use Ramsey\Uuid\Uuid;

class SimpleToolCommandsTest extends CommandTestBaseClass
{
    /**
     * @test
     * @throws \Exception
     */
    public function sendCreateSimpleToolCommand()
    {
        $user = $this->createRandomUser();
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

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

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
     * @throws \Exception
     */
    public function sendCloneToolInstanceCommand()
    {
        $user = $this->createRandomUser();
        $simpleTool = $this->createSimpleTool($user);

        $user2 = $this->createRandomUser();

        $cloneId = Uuid::uuid4()->toString();
        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'cloneToolInstance',
            'metadata' => (object)[],
            'payload' => [
                'id' => $cloneId,
                'base_id' => $simpleTool->id()
            ]
        ];

        $token = $this->getToken($user2->getUsername(), $user2->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var SimpleTool $clone */
        $clone = self::$container->get('doctrine')->getRepository(SimpleTool::class)->findOneById($cloneId);

        $this->assertEquals($simpleTool->tool(), $clone->tool());
        $this->assertEquals($simpleTool->name(), $clone->name());
        $this->assertEquals($simpleTool->description(), $clone->description());
        $this->assertEquals($simpleTool->isPublic(), $clone->isPublic());
        $this->assertEquals($simpleTool->data(), $clone->data());
        $this->assertEquals($user2->getId()->toString(), $clone->userId());
    }



    /**
     * @test
     * @throws \Exception
     */
    public function sendUpdateSimpleToolCommand()
    {
        $user = $this->createRandomUser();
        $simpleTool = $this->createSimpleTool($user);

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'updateToolInstance',
            'metadata' => (object)[],
            'payload' => [
                'id' => $simpleTool->id(),
                'name' => 'ToolNewName',
                'description' => 'ToolNewDescription',
                'public' => true,
                'data' => ['a' => 'very', 'complex' => 'dataset']
            ]
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var SimpleTool $simpleTool */
        $simpleTool = self::$container->get('doctrine')->getRepository(SimpleTool::class)->findOneById($simpleTool->id());
        $this->assertEquals($command['payload']['name'], $simpleTool->name());
        $this->assertEquals($command['payload']['description'], $simpleTool->description());
        $this->assertEquals($command['payload']['public'], $simpleTool->isPublic());
        $this->assertEquals($command['payload']['data'], $simpleTool->data());
        $this->assertEquals($user->getId()->toString(), $simpleTool->userId());
    }

    /**
     * @test
     * @depends sendCreateSimpleToolCommand
     * @throws \Exception
     */
    public function sendUpdateSimpleToolMetadataCommand()
    {
        $user = $this->createRandomUser();
        $simpleTool = $this->createSimpleTool($user);

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'updateToolInstanceMetadata',
            'metadata' => (object)[],
            'payload' => [
                'id' => $simpleTool->id(),
                'name' => 'ToolNewNameUpdated',
                'description' => 'ToolNewDescriptionUpdated',
                'public' => true
            ]
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var SimpleTool $simpleTool */
        $simpleTool = self::$container->get('doctrine')->getRepository(SimpleTool::class)->findOneById($simpleTool->id());
        $this->assertEquals($command['payload']['name'], $simpleTool->name());
        $this->assertEquals($command['payload']['description'], $simpleTool->description());
        $this->assertEquals($command['payload']['public'], $simpleTool->isPublic());
        $this->assertEquals($user->getId()->toString(), $simpleTool->userId());

    }

    /**
     * @test
     * @depends sendCreateSimpleToolCommand
     * @throws \Exception
     */
    public function sendUpdateSimpleToolDataCommand()
    {
        $user = $this->createRandomUser();
        $simpleTool = $this->createSimpleTool($user);

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'updateToolInstanceData',
            'metadata' => (object)[],
            'payload' => [
                'id' => $simpleTool->id(),
                'data' => ['a' => 'very', 'complex' => 'dataset', 'update' => 'now']
            ]
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var SimpleTool $simpleTool */
        $simpleTool = self::$container->get('doctrine')->getRepository(SimpleTool::class)->findOneById($simpleTool->id());
        $this->assertEquals($command['payload']['data'], $simpleTool->data());
        $this->assertEquals($user->getId()->toString(), $simpleTool->userId());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function sendDeleteToolInstanceCommand()
    {
        $user = $this->createRandomUser();
        $simpleTool = $this->createSimpleTool($user);

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'deleteToolInstance',
            'metadata' => (object)[],
            'payload' => [
                'id' => $simpleTool->id()
            ]
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var SimpleTool $simpleTool */
        $simpleTool = self::$container->get('doctrine')->getRepository(SimpleTool::class)->findOneById($simpleTool->id());
        $this->assertTrue($simpleTool->isArchived());
    }
}
