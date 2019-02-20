<?php

namespace App\Tests\Controller;

use App\Model\Modflow\Boundaries;
use App\Model\Modflow\Boundary;
use App\Model\Modflow\Discretization;
use App\Model\Modflow\Layer;
use App\Model\Modflow\ModflowModel;
use App\Model\Modflow\Soilmodel;
use App\Model\ToolMetadata;
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
        $user = $this->createRandomUser();

        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        $modelId = Uuid::uuid4()->toString();

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'createModflowModel',
            'metadata' => (object)[],
            'payload' => [
                'id' => $modelId,
                'name' => 'New numerical groundwater model',
                'description' => 'This is the model description',
                'public' => true,
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

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

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
     * @throws \Exception
     */
    public function sendUpdateModflowModelMetadataCommand(): void
    {
        $user = $this->createRandomUser();
        $model = $this->createRandomModflowModel($user);

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'updateModflowModelMetadata',
            'metadata' => (object)[],
            'payload' => [
                'id' => $model->id(),
                'name' => 'New numerical groundwater model - updated',
                'description' => 'This is the model description - updated',
                'public' => false
            ],
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var ModflowModel $modflowModel */
        $modflowModel = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($model->id());
        $this->assertInstanceOf(ModflowModel::class, $modflowModel);

        $this->assertEquals('T03', $modflowModel->tool());
        $this->assertEquals($command['payload']['name'], $modflowModel->name());
        $this->assertEquals($command['payload']['description'], $modflowModel->description());
        $this->assertEquals($command['payload']['public'], $modflowModel->isPublic());
        $this->assertEquals($user->getId()->toString(), $modflowModel->getUser()->getId()->toString());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function sendUpdateModflowModelDiscretizationCommand(): void
    {
        $user = $this->createRandomUser();
        $model = $this->createRandomModflowModel($user);

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'updateModflowModelDiscretization',
            'metadata' => (object)[],
            'payload' => [
                'id' => $model->id(),
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
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var ModflowModel $modflowModel */
        $modflowModel = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($model->id());
        $this->assertInstanceOf(ModflowModel::class, $modflowModel);

        $this->assertEquals('T03', $modflowModel->tool());
        $this->assertEquals($user->getId()->toString(), $modflowModel->getUser()->getId()->toString());
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
     * @throws \Exception
     */
    public function sendUpdateModflowModelStressperiodsCommand(): void
    {
        $user = $this->createRandomUser();
        $model = $this->createRandomModflowModel($user);

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'updateStressperiods',
            'metadata' => (object)[],
            'payload' => [
                'id' => $model->id(),
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
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var ModflowModel $modflowModel */
        $modflowModel = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($model->id());
        $this->assertInstanceOf(ModflowModel::class, $modflowModel);
        $this->assertInstanceOf(Discretization::class, $modflowModel->discretization());
        $this->assertEquals($command['payload']['stressperiods'], $modflowModel->discretization()->stressperiods());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function sendUpdateMt3dmsCommand(): void
    {
        $user = $this->createRandomUser();
        $model = $this->createRandomModflowModel($user);

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'updateMt3dms',
            'metadata' => (object)[],
            'payload' => [
                'id' => $model->id(),
                'mt3dms' => ['mt3dms-content']
            ]
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var ModflowModel $modflowModel */
        $modflowModel = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($model->id());
        $this->assertInstanceOf(ModflowModel::class, $modflowModel);
        $this->assertEquals($command['payload']['mt3dms'], $modflowModel->transport()->toArray());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function sendCloneModflowModelAsToolCommand(): void
    {
        $user = $this->createRandomUser();
        $model = $this->createRandomModflowModel($user);

        $cloneId = Uuid::uuid4()->toString();

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'cloneModflowModel',
            'metadata' => (object)[],
            'payload' => [
                'id' => $model->id(),
                'new_id' => $cloneId,
                'is_tool' => true
            ]
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var ModflowModel $original */
        $original = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($model->id());

        /** @var ModflowModel $clone */
        $clone = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($cloneId);

        $this->assertEquals($clone->toArray(), $original->toArray());
        $this->assertFalse($clone->isScenario());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function sendCloneModflowModelAsScenarioCommand(): void
    {
        $user = $this->createRandomUser();
        $model = $this->createRandomModflowModel($user);

        $cloneId = Uuid::uuid4()->toString();

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'cloneModflowModel',
            'metadata' => (object)[],
            'payload' => [
                'id' => $model->id(),
                'new_id' => $cloneId,
                'is_tool' => false
            ]
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var ModflowModel $original */
        $original = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($model->id());

        /** @var ModflowModel $clone */
        $clone = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($cloneId);

        $this->assertEquals($clone->toArray(), $original->toArray());
        $this->assertTrue($clone->isScenario());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function sendDeleteModflowModelCommand()
    {
        $user = $this->createRandomUser();
        $model = $this->createRandomModflowModel($user);
        $this->assertFalse($model->isArchived());

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'deleteModflowModel',
            'metadata' => (object)[],
            'payload' => [
                'id' => $model->id(),
            ]
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var ModflowModel $modflowModel */
        $modflowModel = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($model->id());
        $this->assertTrue($modflowModel->isArchived());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function sendAddBoundaryCommand(): void
    {
        $user = $this->createRandomUser();
        $modelId = $this->createRandomModflowModel($user)->id();

        $boundaryId = Uuid::uuid4()->toString();

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'addBoundary',
            'metadata' => (object)[],
            'payload' => [
                'id' => $modelId,
                'boundary' => [
                    'id' => $boundaryId,
                    'name' => 'New wel-Boundary',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [13, 52]
                    ],
                    'type' => 'wel',
                    'active_cells' => [[1, 1]],
                    'affected_layers' => [0],
                    'metadata' => ['well_type' => 'puw'],
                    'date_time_values' => [[
                        'date_time' => '2005-05-17T00:00:00Z',
                        'values' => [0],
                    ]],
                ],
            ],
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var ModflowModel $modflowModel */
        $modflowModel = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($modelId);
        $this->assertEquals($command['payload']['boundary'], $modflowModel->boundaries()->findById($boundaryId)->toArray());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function sendUpdateBoundaryCommand(): void
    {
        $user = $this->createRandomUser();
        $model = $this->createRandomModflowModel($user);

        $boundary = $model->boundaries()->first();
        $updatedBoundary = Boundary::fromArray([
            'id' => $boundary->id(),
            'name' => 'Updated',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [12, 52]
            ],
            'type' => 'wel',
            'active_cells' => [[2, 1]],
            'affected_layers' => [1],
            'metadata' => ['well_type' => 'puw'],
            'date_time_values' => [[
                'date_time' => '2005-05-11T00:00:00Z',
                'values' => [2],
            ]],
        ]);

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'updateBoundary',
            'metadata' => (object)[],
            'payload' => [
                'id' => $model->id(),
                'boundary_id' => $boundary->id(),
                'boundary' => $updatedBoundary->toArray()
            ],
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var ModflowModel $modflowModel */
        $modflowModel = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($model->id());
        $expected = [$command['payload']['boundary']['id'] => $command['payload']['boundary']];
        $this->assertEquals($expected, $modflowModel->boundaries()->toArray());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function sendRemoveBoundaryCommand(): void
    {
        $user = $this->createRandomUser();
        $model = $this->createRandomModflowModel($user);

        $boundaryId = $model->boundaries()->first()->id();
        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'removeBoundary',
            'metadata' => (object)[],
            'payload' => [
                'id' => $model->id(),
                'boundary_id' => $boundaryId,
            ],
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var ModflowModel $modflowModel */
        $modflowModel = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($model->id());
        $this->assertCount(0, $modflowModel->boundaries()->toArray());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function sendAddLayerCommand(): void
    {
        $user = $this->createRandomUser();
        $modelId = $this->createRandomModflowModel($user)->id();

        $layerId = Uuid::uuid4()->toString();

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'addLayer',
            'metadata' => (object)[],
            'payload' => [
                'id' => $modelId,
                'layer' => [
                    'id' => $layerId,
                    'name' => 'Added layer',
                    'description' => 'Added layer description',
                    'number' => 2,
                    'top' => 10,
                    'botm' => -10,
                    'hk' => 100,
                    'hani' => 2,
                    'vka' => 10,
                    'layavg' => 2,
                    'laytyp' => 2,
                    'laywet' => 2,
                    'ss' => 0.3,
                    'sy' => 0.3
                ],
            ],
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var ModflowModel $modflowModel */
        $modflowModel = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($modelId);
        $this->assertEquals($command['payload']['layer'], $modflowModel->soilmodel()->findLayer($layerId)->toArray());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function sendUpdateLayerCommand(): void
    {
        $user = $this->createRandomUser();
        $model = $this->createRandomModflowModel($user);

        $layerId = $model->soilmodel()->firstLayer()->id();
        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'updateLayer',
            'metadata' => (object)[],
            'payload' => [
                'id' => $model->id(),
                'layer' => [
                    'id' => $layerId,
                    'name' => 'Updated layer',
                    'description' => 'Updated layer description',
                    'number' => 3,
                    'top' => 11,
                    'botm' => -11,
                    'hk' => 101,
                    'hani' => 3,
                    'vka' => 11,
                    'layavg' => 12,
                    'laytyp' => 12,
                    'laywet' => 12,
                    'ss' => 0.23,
                    'sy' => 0.23
                ],
            ],
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var ModflowModel $modflowModel */
        $modflowModel = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($model->id());
        $this->assertEquals($command['payload']['layer'], $modflowModel->soilmodel()->findLayer($layerId)->toArray());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function sendRemoveLayerCommand(): void
    {
        $user = $this->createRandomUser();
        $model = $this->createRandomModflowModel($user);

        $layerId = $model->soilmodel()->firstLayer()->id();
        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'removeLayer',
            'metadata' => (object)[],
            'payload' => [
                'id' => $model->id(),
                'layer_id' => $layerId
            ],
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var ModflowModel $modflowModel */
        $modflowModel = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($model->id());
        $this->assertNull($modflowModel->soilmodel()->findLayer($layerId));
    }

    /**
     * @test
     * @throws \Exception
     */
    public function sendUpdateSoilmodelPropertiesCommand(): void
    {
        $user = $this->createRandomUser();
        $model = $this->createRandomModflowModel($user);

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'updateSoilmodelProperties',
            'metadata' => (object)[],
            'payload' => [
                'id' => $model->id(),
                'properties' => ['the' => 'new', 'properties' => 1, 2, 3]
            ],
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('v3/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var ModflowModel $modflowModel */
        $modflowModel = self::$container->get('doctrine')->getRepository(ModflowModel::class)->findOneById($model->id());
        $this->assertEquals($command['payload']['properties'] ,$modflowModel->soilmodel()->properties());
    }
    
    /**
     * @param User $user
     * @return ModflowModel
     * @throws \Exception
     */
    private function createRandomModflowModel(User $user): ModflowModel
    {
        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();

        $modelId = Uuid::uuid4()->toString();
        $modflowModel = ModflowModel::createWithParams(
            $modelId,
            $user,
            'T03',
            ToolMetadata::fromParams(
                sprintf('Model-Name %d', rand(1000000, 10000000 - 1)),
                sprintf('Model-Description %d', rand(1000000, 10000000 - 1)),
                true
            )
        );

        # Discretization
        $discretization = Discretization::fromArray([
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
        ]);
        $modflowModel->setDiscretization($discretization);

        # Boundaries
        $boundary = Boundary::fromArray([
            'id' => Uuid::uuid4()->toString(),
            'name' => 'New wel-Boundary',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [13, 52]
            ],
            'type' => 'wel',
            'active_cells' => [[1, 1]],
            'affected_layers' => [0],
            'metadata' => ['well_type' => 'puw'],
            'date_time_values' => [[
                'date_time' => '2005-05-17T00:00:00Z',
                'values' => [0],
            ]],
        ]);
        $boundaries = Boundaries::create();
        $boundaries->addBoundary($boundary);
        $modflowModel->setBoundaries($boundaries);

        # Soilmodel
        $soilmodel = Soilmodel::create();
        $layer = Layer::fromArray([
            'id' => Uuid::uuid4()->toString(),
            'name' => 'Default layer',
            'description' => 'Default layer description',
            'number' => 1,
            'top' => 0,
            'botm' => -100,
            'hk' => 200,
            'hani' => 1,
            'vka' => 20,
            'layavg' => 1,
            'laytyp' => 1,
            'laywet' => 1,
            'ss' => 0.2,
            'sy' => 0.2
        ]);
        $soilmodel->addLayer($layer);
        $modflowModel->setSoilmodel($soilmodel);

        $em->persist($modflowModel);
        $em->flush();

        return $modflowModel;
    }
}
