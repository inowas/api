<?php

namespace App\Tests\Controller;

use App\Model\Mcda\Criterion;
use App\Model\Mcda\Mcda;
use App\Model\ToolMetadata;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class McdaCommandsTest extends CommandTestBaseClass
{
    /**
     * @test
     * @throws \Exception
     */
    public function sendCreateToolInstanceCommand()
    {
        $user = $this->createRandomUser();

        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        $mcdaId = Uuid::uuid4()->toString();

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'createToolInstance',
            'metadata' => (object)[],
            'payload' => [
                'tool' => 'T05',
                'id' => $mcdaId,
                'name' => 'New Mcda',
                'description' => 'This Mcda description',
                'public' => true,
                'data' => [
                    'criteria' => ['abc' => 'def'],
                    'weightAssignments' => ['ghi' => 'jkl'],
                    'constraints' => ['mno' => 'pqr'],
                    'withAhp' => true,
                    'suitability' => ['stu' => 'vwx']
                ]
            ],
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var Mcda $mcda */
        $mcda = self::$container->get('doctrine')->getRepository(Mcda::class)->findOneById($mcdaId);
        $this->assertInstanceOf(Mcda::class, $mcda);
        $this->assertEquals($command['payload']['tool'], $mcda->tool());
        $this->assertEquals($command['payload']['name'], $mcda->name());
        $this->assertEquals($command['payload']['description'], $mcda->description());
        $this->assertEquals($command['payload']['public'], $mcda->isPublic());
        $this->assertEquals($user->getId()->toString(), $mcda->getUser()->getId()->toString());

        $this->assertEquals($command['payload']['data']['criteria'], $mcda->critera());
        $this->assertEquals($command['payload']['data']['constraints'], $mcda->constraints());
        $this->assertEquals($command['payload']['data']['suitability'], $mcda->suitability());
        $this->assertEquals($command['payload']['data']['weightAssignments'], $mcda->weightAssignments());
        $this->assertEquals($command['payload']['data']['withAhp'], $mcda->withAhp());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function sendUpdateCriterionCommand(): void
    {
        $user = $this->createRandomUser();
        $mcda = $this->createRandomMcda($user);

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'mcdaUpdateCriterion',
            'metadata' => (object)[],
            'payload' => [
                'id' => $mcda->id(),
                'criterion' => [
                    'id' => Uuid::uuid4()->toString(),
                    'data' => 'data-goes-here'
                ]
            ],
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());

        /** @var Mcda $mcda */
        $mcda = self::$container->get('doctrine')->getRepository(Mcda::class)->findOneById($mcda->id());
        $this->assertEquals($command['payload']['criterion'], $mcda->findCriterion($command['payload']['criterion']['id'])->toArray());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function sendDeleteCriterionCommand(): void
    {
        $user = $this->createRandomUser();
        $mcda = $this->createRandomMcda($user);

        /** @noinspection PhpParamsInspection */
        $criterion = Criterion::fromArray(array_values($mcda->critera())[0]);


        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'mcdaDeleteCriterion',
            'metadata' => (object)[],
            'payload' => [
                'id' => $mcda->id(),
                'criterion_id' => $criterion->id()
            ],
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());


        /** @var Mcda $mcda */
        $mcda = self::$container->get('doctrine')->getRepository(Mcda::class)->findOneById($mcda->id());
        $this->assertCount(0, $mcda->critera());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function sendMcdaUpdateProjectCommand(): void
    {
        $user = $this->createRandomUser();
        $mcda = $this->createRandomMcda($user);

        $command = [
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'mcdaUpdateProject',
            'metadata' => (object)[],
            'payload' => [
                'id' => $mcda->id(),
                'data' => [
                    'weightAssignments' => ['ghi1' => 'jk11'],
                    'constraints' => ['mno1' => 'pqr1'],
                    'withAhp' => false,
                    'suitability' => ['stu1' => 'vwx1']
                ]
            ],
        ];

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendCommand('api/messagebox', $command, $token);
        $this->assertEquals(202, $response->getStatusCode());


        /** @var Mcda $updatedMcda */
        $updatedMcda = self::$container->get('doctrine')->getRepository(Mcda::class)->findOneById($mcda->id());
        $this->assertInstanceOf(Mcda::class, $updatedMcda);
        $this->assertEquals($mcda->critera(), $updatedMcda->critera());
        $this->assertEquals($command['payload']['data']['constraints'], $updatedMcda->constraints());
        $this->assertEquals($command['payload']['data']['suitability'], $updatedMcda->suitability());
        $this->assertEquals($command['payload']['data']['weightAssignments'], $updatedMcda->weightAssignments());
        $this->assertEquals($command['payload']['data']['withAhp'], $updatedMcda->withAhp());
    }

    /**
     * @param User $user
     * @return Mcda
     * @throws \Exception
     */
    private function createRandomMcda(User $user): Mcda
    {
        $mcdaId = Uuid::uuid4()->toString();
        $mcda = Mcda::createWithParams(
            $mcdaId,
            $user,
            'T05',
            ToolMetadata::fromParams(
                sprintf('Mcda-Name %d', rand(1000000, 10000000 - 1)),
                sprintf('Mcda-Description %d', rand(1000000, 10000000 - 1)),
                true
            )
        );

        $criterionId = Uuid::uuid4()->toString();

        $mcda->setData([
            'criteria' => [
                $criterionId => [
                    'id' => $criterionId,
                    'data' => 'data'
                ]],
            'weightAssignments' => ['ghi' => 'jkl'],
            'constraints' => ['mno' => 'pqr'],
            'withAhp' => true,
            'suitability' => ['stu' => 'vwx']
        ]);

        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($mcda);
        $em->flush();

        return $mcda;
    }
}
