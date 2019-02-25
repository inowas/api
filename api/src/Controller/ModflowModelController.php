<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Modflow\ModflowModel;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class ModflowModelController
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
     * @Route("/modflowmodels/{id}", name="modflowmodel_details", methods={"GET"})
     * @param string $id
     * @return JsonResponse
     */
    public function index(string $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        $permissions = '---';

        if ($modflowModel->isPublic()) {
            $permissions = 'r--';
        }

        if ($modflowModel->userId() === $user->getId()->toString()) {
            $permissions = 'rwx';
        }

        if ($permissions === '---') {
            return new JsonResponse([], 403);
        }

        $result = [
            'id' => $modflowModel->id(),
            'name' => $modflowModel->name(),
            'description' => $modflowModel->description(),
            'permissions' => $permissions,
            'public' => $modflowModel->isPublic(),
            'tool' => $modflowModel->tool(),
            'discretization' => $modflowModel->discretization()->toArray()
        ];

        return new JsonResponse($result);
    }

    /**
     * @Route("/modflowmodels/{id}/discretization", name="modflowmodel_discretization", methods={"GET"})
     * @param string $id
     * @return JsonResponse
     */
    public function indexDiscretization(string $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        $permissions = '---';

        if ($modflowModel->isPublic()) {
            $permissions = 'r--';
        }

        if ($modflowModel->userId() === $user->getId()->toString()) {
            $permissions = 'rwx';
        }

        if ($permissions === '---') {
            return new JsonResponse([], 403);
        }

        $result = $modflowModel->discretization()->toArray();
        return new JsonResponse($result);
    }

    /**
     * @Route("/modflowmodels/{id}/soilmodel", name="modflowmodel_soilmodel", methods={"GET"})
     * @param string $id
     * @return JsonResponse
     */
    public function indexSoilmodel(string $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        $permissions = '---';

        if ($modflowModel->isPublic()) {
            $permissions = 'r--';
        }

        if ($modflowModel->userId() === $user->getId()->toString()) {
            $permissions = 'rwx';
        }

        if ($permissions === '---') {
            return new JsonResponse([], 403);
        }

        $result = $modflowModel->soilmodel()->toArray();
        return new JsonResponse($result);
    }

    /**
     * @Route("/modflowmodels/{id}/boundaries", name="modflowmodel_boundaries", methods={"GET"})
     * @param string $id
     * @return JsonResponse
     */
    public function indexBoundaries(string $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        $permissions = '---';

        if ($modflowModel->isPublic()) {
            $permissions = 'r--';
        }

        if ($modflowModel->userId() === $user->getId()->toString()) {
            $permissions = 'rwx';
        }

        if ($permissions === '---') {
            return new JsonResponse([], 403);
        }

        $result = $modflowModel->boundaries()->toArray();
        return new JsonResponse($result);
    }

    /**
     * @Route("/modflowmodels/{id}/transport", name="modflowmodel_transport", methods={"GET"})
     * @param string $id
     * @return JsonResponse
     */
    public function indexTransport(string $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        $permissions = '---';

        if ($modflowModel->isPublic()) {
            $permissions = 'r--';
        }

        if ($modflowModel->userId() === $user->getId()->toString()) {
            $permissions = 'rwx';
        }

        if ($permissions === '---') {
            return new JsonResponse([], 403);
        }

        $result = $modflowModel->transport()->toArray();
        return new JsonResponse($result);
    }

    /**
     * @Route("/modflowmodels/{id}/calculation", name="modflowmodel_calculation", methods={"GET"})
     * @param string $id
     * @return JsonResponse
     */
    public function indexCalculation(string $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        $permissions = '---';

        if ($modflowModel->isPublic()) {
            $permissions = 'r--';
        }

        if ($modflowModel->userId() === $user->getId()->toString()) {
            $permissions = 'rwx';
        }

        if ($permissions === '---') {
            return new JsonResponse([], 403);
        }

        $result = $modflowModel->calculation()->toArray();
        return new JsonResponse($result);
    }
}
