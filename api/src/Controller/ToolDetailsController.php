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


class ToolDetailsController
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
     * @Route("/tools/{tool}/{id}", name="tool_data", methods={"GET"})
     * @param Request $request
     * @param string $tool
     * @param string $id
     * @return JsonResponse
     */
    public function __invoke(Request $request, string $tool, string $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

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

        $permissions = '---';

        if ($toolInstance->isPublic()) {
            $permissions = 'r--';
        }

        if ($toolInstance->userId() === $user->getId()->toString()) {
            $permissions = 'rwx';
        }

        if ($permissions === '---') {
            return new JsonResponse([]);
        }

        $result = [
            'id' => $toolInstance->id(),
            'name' => $toolInstance->name(),
            'description' => $toolInstance->description(),
            'permissions' => $permissions,
            'public' => $toolInstance->isPublic(),
            'tool' => $toolInstance->tool(),
            'data' => $toolInstance->data()
        ];

        return new JsonResponse($result);
    }
}
