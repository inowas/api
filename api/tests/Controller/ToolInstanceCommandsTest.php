<?php

namespace App\Tests\Controller;

use App\Model\DashboardItem;
use App\Model\SimpleToolInstance;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class ToolInstanceCommandsTest extends CommandTestBaseClass
{
    /**
     * @test
     * @throws \Exception
     */
    public function sendCreateToolInstanceCommand()
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

        /** @var SimpleToolInstance $simpleTool */
        $simpleTool = self::$container->get('doctrine')->getRepository(SimpleToolInstance::class)->findOneById($toolInstanceId);
        $this->assertEquals($command['payload']['tool'], $simpleTool->getTool());
        $this->assertEquals($command['payload']['name'], $simpleTool->getName());
        $this->assertEquals($command['payload']['description'], $simpleTool->getDescription());
        $this->assertEquals($command['payload']['public'], $simpleTool->isPublic());
        $this->assertEquals($command['payload']['data'], $simpleTool->getData());
        $this->assertEquals($user->getId()->toString(), $simpleTool->getUserId());

        /** @var DashboardItem $dashboardItem */
        $dashboardItem = self::$container->get('doctrine')->getRepository(DashboardItem::class)->findOneById($toolInstanceId);
        $this->assertEquals($command['payload']['tool'], $dashboardItem->getTool());
        $this->assertEquals($command['payload']['name'], $dashboardItem->getName());
        $this->assertEquals($command['payload']['description'], $dashboardItem->getDescription());
        $this->assertEquals($command['payload']['public'], $dashboardItem->isPublic());
        $this->assertEquals($username, $dashboardItem->getUsername());
        $this->assertEquals($user->getId()->toString(), $dashboardItem->getUserId());

        return ['user' => $user, 'command' => $command];
    }

    /**
     * @test
     * @depends sendCreateToolInstanceCommand
     * @param array $data
     * @throws \Exception
     */
    public function createToolInstanceCommandSimpleToolsProjection(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];
        $command = $data['command'];
        $toolInstanceId = $command['payload']['id'];

        /** @var SimpleToolInstance $simpleTool */
        $simpleTool = self::$container->get('doctrine')->getRepository(SimpleToolInstance::class)->findOneById($toolInstanceId);
        $this->assertEquals($command['payload']['tool'], $simpleTool->getTool());
        $this->assertEquals($command['payload']['name'], $simpleTool->getName());
        $this->assertEquals($command['payload']['description'], $simpleTool->getDescription());
        $this->assertEquals($command['payload']['public'], $simpleTool->isPublic());
        $this->assertEquals($command['payload']['data'], $simpleTool->getData());
        $this->assertEquals($user->getId()->toString(), $simpleTool->getUserId());
    }

    /**
     * @test
     * @depends sendCreateToolInstanceCommand
     * @param array $data
     * @throws \Exception
     */
    public function createToolInstanceCommandDashboardProjection(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];
        $command = $data['command'];
        $toolInstanceId = $command['payload']['id'];

        /** @var DashboardItem $dashboardItem */
        $dashboardItem = self::$container->get('doctrine')->getRepository(DashboardItem::class)->findOneById($toolInstanceId);
        $this->assertEquals($command['payload']['tool'], $dashboardItem->getTool());
        $this->assertEquals($command['payload']['name'], $dashboardItem->getName());
        $this->assertEquals($command['payload']['description'], $dashboardItem->getDescription());
        $this->assertEquals($command['payload']['public'], $dashboardItem->isPublic());
        $this->assertEquals($user->getUsername(), $dashboardItem->getUsername());
        $this->assertEquals($user->getId()->toString(), $dashboardItem->getUserId());
    }

    /**
     * @test
     * @depends sendCreateToolInstanceCommand
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
    public function cloneToolInstanceCommandSimpleToolsProjection(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];
        $command = $data['command'];
        $newId = $command['payload']['id'];
        $baseId = $command['payload']['base_id'];

        $simpleTools = self::$container->get('doctrine')->getRepository(SimpleToolInstance::class)->findAll();
        $this->assertCount(2, $simpleTools);

        /** @var SimpleToolInstance $oldSimpleTool */
        $oldSimpleTool = self::$container->get('doctrine')->getRepository(SimpleToolInstance::class)->findOneById($baseId);

        /** @var SimpleToolInstance $simpleTool */
        $simpleTool = self::$container->get('doctrine')->getRepository(SimpleToolInstance::class)->findOneById($newId);

        $this->assertEquals($oldSimpleTool->getTool(), $simpleTool->getTool());
        $this->assertEquals($oldSimpleTool->getName(), $simpleTool->getName());
        $this->assertEquals($oldSimpleTool->getDescription(), $simpleTool->getDescription());
        $this->assertEquals($oldSimpleTool->isPublic(), $simpleTool->isPublic());
        $this->assertEquals($oldSimpleTool->getData(), $simpleTool->getData());
        $this->assertEquals($user->getId()->toString(), $simpleTool->getUserId());
    }


    /**
     * @test
     * @depends sendCloneToolInstanceCommand
     * @param array $data
     * @throws \Exception
     */
    public function cloneToolInstanceCommandDashboardProjection(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];
        $command = $data['command'];
        $newId = $command['payload']['id'];
        $baseId = $command['payload']['base_id'];

        $dashBoardItems = self::$container->get('doctrine')->getRepository(DashboardItem::class)->findAll();
        $this->assertCount(2, $dashBoardItems);

        /** @var DashboardItem $oldDashboardItem */
        $oldDashboardItem = self::$container->get('doctrine')->getRepository(DashboardItem::class)->findOneById($baseId);

        /** @var DashboardItem $dashboardItem */
        $dashboardItem = self::$container->get('doctrine')->getRepository(DashboardItem::class)->findOneById($newId);

        $this->assertEquals($oldDashboardItem->getTool(), $dashboardItem->getTool());
        $this->assertEquals($oldDashboardItem->getName(), $dashboardItem->getName());
        $this->assertEquals($oldDashboardItem->getDescription(), $dashboardItem->getDescription());
        $this->assertEquals($oldDashboardItem->isPublic(), $dashboardItem->isPublic());
        $this->assertEquals($user->getId()->toString(), $dashboardItem->getUserId());
    }

    /**
     * @test
     * @depends sendCreateToolInstanceCommand
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function sendUpdateCloneToolInstanceCommand(array $data)
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
     * @depends sendUpdateCloneToolInstanceCommand
     * @param array $data
     * @throws \Exception
     */
    public function updateToolInstanceCommandDashboardProjection(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];
        $command = $data['command'];
        $toolInstanceId = $command['payload']['id'];

        /** @var DashboardItem $dashboardItem */
        $dashboardItem = self::$container->get('doctrine')->getRepository(DashboardItem::class)->findOneById($toolInstanceId);
        $this->assertEquals($command['payload']['name'], $dashboardItem->getName());
        $this->assertEquals($command['payload']['description'], $dashboardItem->getDescription());
        $this->assertEquals($command['payload']['public'], $dashboardItem->isPublic());
        $this->assertEquals($user->getUsername(), $dashboardItem->getUsername());
        $this->assertEquals($user->getId()->toString(), $dashboardItem->getUserId());
    }

    /**
     * @test
     * @depends sendUpdateCloneToolInstanceCommand
     * @param array $data
     * @throws \Exception
     */
    public function updateToolInstanceCommandSimpleToolsProjection(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];
        $command = $data['command'];
        $toolInstanceId = $command['payload']['id'];

        /** @var SimpleToolInstance $simpleTool */
        $simpleTool = self::$container->get('doctrine')->getRepository(SimpleToolInstance::class)->findOneById($toolInstanceId);
        $this->assertEquals($command['payload']['name'], $simpleTool->getName());
        $this->assertEquals($command['payload']['description'], $simpleTool->getDescription());
        $this->assertEquals($command['payload']['public'], $simpleTool->isPublic());
        $this->assertEquals($command['payload']['data'], $simpleTool->getData());
        $this->assertEquals($user->getId()->toString(), $simpleTool->getUserId());
    }

    /**
     * @test
     * @depends sendCreateToolInstanceCommand
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
    public function deleteToolInstanceCommandDashboardProjection(array $data)
    {
        static::createClient();

        $command = $data['command'];
        $toolInstanceId = $command['payload']['id'];

        /** @var DashboardItem $dashboardItem */
        $dashboardItem = self::$container->get('doctrine')->getRepository(SimpleToolInstance::class)->findOneById($toolInstanceId);
        $this->assertNull($dashboardItem);
    }

    /**
     * @test
     * @depends sendDeleteToolInstanceCommand
     * @param array $data
     * @throws \Exception
     */
    public function deleteToolInstanceCommandSimpleToolsProjection(array $data)
    {
        static::createClient();

        $command = $data['command'];
        $toolInstanceId = $command['payload']['id'];

        /** @var SimpleToolInstance $simpleTool */
        $simpleTool = self::$container->get('doctrine')->getRepository(SimpleToolInstance::class)->findOneById($toolInstanceId);
        $this->assertNull($simpleTool);
    }
}
