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
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use TweedeGolf\PrometheusClient\CollectorRegistry;
use TweedeGolf\PrometheusClient\PrometheusException;


class ToolDetailsController
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
     * @Route("/tools/{tool}/{id}", name="tool_data", methods={"GET"})
     * @param Request $request
     * @param string $tool
     * @param string $id
     * @return JsonResponse
     * @throws PrometheusException
     */
    public function __invoke(Request $request, string $tool, string $id): JsonResponse
    {
        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        /** @var User $user */
        $user = $token->getUser();

        $metric = $this->collectorRegistry->getCounter('http_requests_total');
        $metric->inc(1, ['handler' => sprintf('/tools/%s', $tool)]);

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

        /** @var ToolInstance $toolInstance */
        $toolInstance = $this->entityManager->getRepository($toolClass)->findOneBy(['id' => $id]);

        /** @var string $permissions */
        $permissions = $toolInstance->getPermissions($user);
        if ($permissions === '---') {
            return new JsonResponse([]);
        }

        $result = [
            'id' => $toolInstance->id(),
            'name' => $toolInstance->name(),
            'description' => $toolInstance->description(),
            'permissions' => $permissions,
            'public' => $toolInstance->isPublic(),
            'created_at' => $toolInstance->createdAt()->format(DATE_ATOM),
            'tool' => $toolInstance->tool(),
            'data' => $toolInstance->data()
        ];

        return new JsonResponse($result);
    }
}
