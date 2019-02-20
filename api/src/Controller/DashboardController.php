<?php

namespace App\Controller;

use App\Model\Mcda\Mcda;
use App\Model\Modflow\ModflowModel;
use App\Model\SimpleTool\SimpleTool;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class DashboardController
{

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var TokenStorageInterface */
    private $tokenStorage;


    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/tools/{tool}", name="dashboard", methods={"GET"})
     * @param Request $request
     * @param string $tool
     * @return JsonResponse
     */
    public function __invoke(Request $request, string $tool): JsonResponse
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        switch ($tool){
            case ('T03'):
                $toolClass = ModflowModel::class;
                break;
            case ('T05'):
                $toolClass = Mcda::class;
                break;
            default:
                $toolClass = SimpleTool::class;
        }

        $getAllPublicInstances = $request->query->has('public') && $request->query->get('public') === 'true';

        if ($getAllPublicInstances) {
            $instances = $this->entityManager->getRepository($toolClass)->findBy([
                'tool' => $tool,
                'isPublic' => true,
                'isScenario' => false,
                'isArchived' => false
            ]);

            return new JsonResponse($instances);
        }

        $instances = $this->entityManager->getRepository($toolClass)->findBy([
            'tool' => $tool,
            'user' => $user,
            'isScenario' => false,
            'isArchived' => false
        ]);

        return new JsonResponse($instances);
    }
}
