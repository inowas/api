<?php

namespace App\Tests\Controller;

use App\Model\SimpleTool\SimpleTool;
use App\Model\ToolMetadata;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class DashboardControllerTest extends CommandTestBaseClass
{
    /**
     * @test
     * @throws \Exception
     */
    public function aUserCanReadHisPrivateTools()
    {
        $user = $this->createRandomUser();
        $privateTool = $this->createSimpleTool($user, false);

        $token = $this->getToken($user->getUsername(), $user->getPassword());
        $response = $this->sendRequest('api/tools/' . $privateTool->tool(), $token);
        $this->assertEquals(200, $response->getStatusCode());
        $tools = json_decode($response->getContent(), true);
        $this->assertCount(1, $tools);
        $this->assertEquals($privateTool->toArray(), $tools[0]);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function aUserCanReadAllPublicTools()
    {
        $user1 = $this->createRandomUser();
        $publicTool1User1 = $this->createSimpleTool($user1, true);
        $this->createSimpleTool($user1, true);
        $this->createSimpleTool($user1, false);

        $user2 = $this->createRandomUser();
        $this->createSimpleTool($user2, true);
        $this->createSimpleTool($user2, true);
        $token = $this->getToken($user2->getUsername(), $user2->getPassword());

        $response = $this->sendRequest('api/tools/' . $publicTool1User1->tool().'/?public=true', $token);
        $this->assertEquals(200, $response->getStatusCode());
        $tools = json_decode($response->getContent(), true);
        $this->assertCount(4, $tools);
    }

    /**
     * @return User
     * @throws \Exception
     */
    private function createRandomUser(): User
    {
        static::createClient();

        $username = sprintf('newUser_%d', rand(1000000, 10000000 - 1));
        $password = sprintf('newUserPassword_%d', rand(1000000, 10000000 - 1));

        $user = new User($username, $password, ['ROLE_USER']);

        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();


        return $user;
    }

    /**
     * @param User $user
     * @param bool $isPublic
     * @return SimpleTool
     * @throws \Exception
     */
    private function createSimpleTool(User $user, bool $isPublic): SimpleTool
    {
        $simpleTool = SimpleTool::createWithParams(
            Uuid::uuid4()->toString(),
            $user->getId(),
            'T02',
            ToolMetadata::fromParams(
                'name',
                'description',
                $isPublic
            )
        );

        $simpleTool->setData(['123' => 456]);

        static::createClient();

        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($simpleTool);
        $em->flush();

        return $simpleTool;
    }
}
