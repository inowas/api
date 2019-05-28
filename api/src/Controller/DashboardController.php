<?php

namespace App\Controller;

use App\Model\Mcda\Mcda;
use App\Model\Modflow\ModflowModel;
use App\Model\SimpleTool\SimpleTool;
use App\Model\ToolInstance;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use TweedeGolf\PrometheusClient\CollectorRegistry;
use TweedeGolf\PrometheusClient\PrometheusException;


final class DashboardController
{

    /** @var CollectorRegistry */
    private $collectorRegistry;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var TokenStorageInterface */
    private $tokenStorage;


    public function __construct(
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage,
        CollectorRegistry $collectorRegistry
    )
    {
        $this->collectorRegistry = $collectorRegistry;
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/tools/{tool}", name="dashboard", methods={"GET"})
     * @param Request $request
     * @param string $tool
     * @return JsonResponse
     * @throws PrometheusException
     */
    public function __invoke(Request $request, string $tool): JsonResponse
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $metric = $this->collectorRegistry->getCounter('http_requests_total');
        $metric->inc(1, ['handler' => '/dashboard']);

        switch ($tool) {
            case ('T03'):
                $toolClass = ModflowModel::class;
                break;
            case ('T05'):
                $toolClass = Mcda::class;
                break;
            default:
                $toolClass = SimpleTool::class;
        }

        $isPublic = $request->query->has('public') && $request->query->get('public') === 'true';

        if ($toolClass === SimpleTool::class) {
            $instances = $this->entityManager
                ->getRepository(SimpleTool::class)
                ->getTool($tool, $user, $isPublic, false);
            return $this->createResponse($instances);
        }

        if ($isPublic) {
            $instances = $this->entityManager->getRepository($toolClass)->findBy([
                'tool' => $tool,
                'isPublic' => true,
                'isScenario' => false,
                'isArchived' => false
            ]);
            return $this->createResponse($instances);
        }

        $instances = $this->entityManager->getRepository($toolClass)->findBy([
            'tool' => $tool,
            'user' => $user,
            'isScenario' => false,
            'isArchived' => false
        ]);

        return $this->createResponse($instances);
    }

    private function createResponse(array $instances): JsonResponse
    {
        /** @var ToolInstance $instance */
        foreach ($instances as $key => $instance) {
            $instances[$key] = [
                'id' => $instance->id(),
                'tool' => $instance->tool(),
                'name' => $instance->name(),
                'description' => $instance->description(),
                'created_at' => $instance->createdAt()->format(DATE_ATOM),
                'updated_at' => $instance->createdAt()->format(DATE_ATOM),
                'user_name' => $instance->getUser()->getUsername()
            ];
        }

        return new JsonResponse($instances);
    }
}
