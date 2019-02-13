<?php

namespace App\Tests\Controller;

use App\Model\Modflow\Discretization;
use App\Model\Modflow\ModflowModel;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class ModflowModelCommandsTest extends CommandTestBaseClass
{
    /**
     * @test
     * @throws \Exception
     */
    public function sendCreateModflowModelCommand()
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
            'message_name' => 'createModflowModel',
            'metadata' => (object)[],
            'payload' => [
                'id' => $toolInstanceId,
                'name' => 'New numerical groundwater model',
                'description' => 'This is the model description',
                'active_cells' => [[0, 1], [1, 1], [0, 0], [1, 0]],
                'bounding_box' => [[13.785759, 51.133180], [13.788094, 51.134608]],
                'geometry' => [
                    'type' => 'Polygon',
                    'coordinates' => [[
                        [13.785759, 51.134162],
                        [13.786697, 51.134608],
                        [13.788094, 51.133921],
                        [13.786680, 51.133180],
                        [13.785759, 51.134162]
                    ]]
                ],
                'grid_size' => [
                    'n_x' => 2,
                    'n_y' => 2,
                ],
                'length_unit' => 2,
                'public' => true,
                'stressperiods' => [
                    'start_date_time' => '2000-01-01T00:00:00.000Z',
                    'end_date_time' => '2019-12-31T00:00:00.000Z',
                    'stressperiods' => [[
                        'totim_start' => 0,
                        'perlen' => 0,
                        'nstp' => 1,
                        'tsmult' => 1,
                        'steady' => true
                    ]],
                    'time_unit' => 4,
                ],
                'time_unit' => 4,
            ],
        ];

        $token = $this->getToken($username, $password);
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        return ['user' => $user, 'command' => $command];
    }

    /**
     * @test
     * @depends sendCreateModflowModelCommand
     * @param array $data
     * @throws \Exception
     */
    public function modflowModelWasStoredCorrectly(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];
        $command = $data['command'];
        $modelId = $command['payload']['id'];

        /** @var ModflowModel $modflowModel */
        $modflowModel = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($modelId);
        $this->assertInstanceOf(ModflowModel::class, $modflowModel);

        $this->assertEquals('T03', $modflowModel->tool());
        $this->assertEquals($command['payload']['name'], $modflowModel->name());
        $this->assertEquals($command['payload']['description'], $modflowModel->description());
        $this->assertEquals($command['payload']['public'], $modflowModel->isPublic());
        $this->assertEquals($user->getId()->toString(), $modflowModel->userId());

        $this->assertInstanceOf(Discretization::class, $modflowModel->discretization());
        $expected = Discretization::fromParams(
            $command['payload']['geometry'],
            $command['payload']['bounding_box'],
            $command['payload']['grid_size'],
            $command['payload']['active_cells'],
            $command['payload']['stressperiods'],
            $command['payload']['length_unit'],
            $command['payload']['time_unit']
        );
        $this->assertEquals($expected, $modflowModel->discretization());
    }

    /**
     * @test
     * @depends sendCreateModflowModelCommand
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function sendUpdateModflowModelMetadataCommand(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];
        $toolInstanceId = $data['command']['payload']['id'];

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'updateModflowModelMetadata',
            'metadata' => (object)[],
            'payload' => [
                'id' => $toolInstanceId,
                'name' => 'New numerical groundwater model - updated',
                'description' => 'This is the model description - updated',
                'public' => false
            ],
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        return ['user' => $user, 'command' => $command];
    }

    /**
     * @test
     * @depends sendUpdateModflowModelMetadataCommand
     * @param array $data
     * @throws \Exception
     */
    public function modflowModelMetadataWasUpdatedCorrectly(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];
        $command = $data['command'];
        $modelId = $command['payload']['id'];

        /** @var ModflowModel $modflowModel */
        $modflowModel = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($modelId);
        $this->assertInstanceOf(ModflowModel::class, $modflowModel);

        $this->assertEquals('T03', $modflowModel->tool());
        $this->assertEquals($command['payload']['name'], $modflowModel->name());
        $this->assertEquals($command['payload']['description'], $modflowModel->description());
        $this->assertEquals($command['payload']['public'], $modflowModel->isPublic());
        $this->assertEquals($user->getId()->toString(), $modflowModel->userId());
    }

    /**
     * @test
     * @depends sendCreateModflowModelCommand
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function sendUpdateModflowModelDiscretizationCommand(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];
        $toolInstanceId = $data['command']['payload']['id'];

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'updateModflowModelDiscretization',
            'metadata' => (object)[],
            'payload' => [
                'id' => $toolInstanceId,
                'active_cells' => [[0, 1], [1, 1], [0, 0], [1, 0], [10, 10]],
                'bounding_box' => [[13, 51], [14, 52]],
                'geometry' => [
                    'type' => 'Polygon',
                    'coordinates' => [[
                        [13, 51],
                        [13.786697, 51.134608],
                        [13.788094, 51.133921],
                        [13.786680, 51.133180],
                        [13, 51]
                    ]]
                ],
                'grid_size' => [
                    'n_x' => 2,
                    'n_y' => 4,
                ],
                'length_unit' => 3,
                'stressperiods' => [
                    'start_date_time' => '2000-01-02T00:00:00.000Z',
                    'end_date_time' => '2019-12-30T00:00:00.000Z',
                    'stressperiods' => [[
                        'totim_start' => 1,
                        'perlen' => 1,
                        'nstp' => 2,
                        'tsmult' => 2,
                        'steady' => false
                    ]],
                    'time_unit' => 3,
                ],
                'time_unit' => 3,
            ],
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        return ['user' => $user, 'command' => $command];
    }

    /**
     * @test
     * @depends sendUpdateModflowModelDiscretizationCommand
     * @param array $data
     * @throws \Exception
     */
    public function modflowModelDiscretizationWasUpdatedCorrectly(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];
        $command = $data['command'];
        $modelId = $command['payload']['id'];

        /** @var ModflowModel $modflowModel */
        $modflowModel = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($modelId);
        $this->assertInstanceOf(ModflowModel::class, $modflowModel);

        $this->assertEquals('T03', $modflowModel->tool());
        $this->assertEquals($user->getId()->toString(), $modflowModel->userId());
        $this->assertInstanceOf(Discretization::class, $modflowModel->discretization());
        $expected = Discretization::fromParams(
            $command['payload']['geometry'],
            $command['payload']['bounding_box'],
            $command['payload']['grid_size'],
            $command['payload']['active_cells'],
            $command['payload']['stressperiods'],
            $command['payload']['length_unit'],
            $command['payload']['time_unit']
        );
        $this->assertEquals($expected, $modflowModel->discretization());
    }

    /**
     * @test
     * @depends sendCreateModflowModelCommand
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function sendUpdateModflowModelStressperiodsCommand(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];
        $toolInstanceId = $data['command']['payload']['id'];

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'updateStressperiods',
            'metadata' => (object)[],
            'payload' => [
                'id' => $toolInstanceId,
                'stressperiods' => [
                    'start_date_time' => '2000-01-03T00:00:00.000Z',
                    'end_date_time' => '2019-12-29T00:00:00.000Z',
                    'stressperiods' => [[
                        'totim_start' => 2,
                        'perlen' => 3,
                        'nstp' => 4,
                        'tsmult' => 4,
                        'steady' => true
                    ]],
                    'time_unit' => 3,
                ]
            ]
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        return ['user' => $user, 'command' => $command];
    }

    /**
     * @test
     * @depends sendUpdateModflowModelStressperiodsCommand
     * @param array $data
     * @throws \Exception
     */
    public function modflowModelStressperiodsWereUpdatedCorrectly(array $data)
    {
        static::createClient();

        /** @var User $user */
        $user = $data['user'];
        $command = $data['command'];
        $modelId = $command['payload']['id'];

        /** @var ModflowModel $modflowModel */
        $modflowModel = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($modelId);
        $this->assertInstanceOf(ModflowModel::class, $modflowModel);
        $this->assertEquals('T03', $modflowModel->tool());
        $this->assertEquals($user->getId()->toString(), $modflowModel->userId());
        $this->assertInstanceOf(Discretization::class, $modflowModel->discretization());
        $this->assertEquals($command['payload']['stressperiods'], $modflowModel->discretization()->stressperiods());
    }
}
