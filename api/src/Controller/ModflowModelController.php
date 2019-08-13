<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Modflow\Layer;
use App\Model\Modflow\ModflowModel;
use App\Model\Modflow\Soilmodel;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use TweedeGolf\PrometheusClient\CollectorRegistry;
use TweedeGolf\PrometheusClient\PrometheusException;


class ModflowModelController
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
     * @Route("/modflowmodels/{id}", name="modflowmodel_details", methods={"GET"})
     * @param string $id
     * @return JsonResponse
     * @throws PrometheusException
     */
    public function index(string $id): JsonResponse
    {
        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        /** @var User $user */
        $user = $token->getUser();

        $this->writeMetrics('/modflowmodels');

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        /** @var string $permissions */
        $permissions = $modflowModel->getPermissions($user);

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
            'discretization' => $modflowModel->discretization()->toArray(),
            'calculation_id' => $modflowModel->calculation()->latest()
        ];

        return new JsonResponse($result);
    }

    /**
     * @Route("/modflowmodels/{id}/discretization", name="modflowmodel_discretization", methods={"GET"})
     * @param string $id
     * @return JsonResponse
     * @throws PrometheusException
     */
    public function indexDiscretization(string $id): JsonResponse
    {
        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        /** @var User $user */
        $user = $token->getUser();

        $this->writeMetrics('/modflowmodels');

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        if ($modflowModel->getPermissions($user) === '---') {
            return new JsonResponse([], 403);
        }

        $result = $modflowModel->discretization()->toArray();
        return new JsonResponse($result);
    }

    /**
     * @Route("/modflowmodels/{id}/soilmodel", name="modflowmodel_soilmodel", methods={"GET"})
     * @param string $id
     * @return JsonResponse
     * @throws PrometheusException
     */
    public function indexSoilmodel(string $id): JsonResponse
    {
        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        /** @var User $user */
        $user = $token->getUser();

        $this->writeMetrics('/modflowmodels');

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        if ($modflowModel->getPermissions($user) === '---') {
            return new JsonResponse([], 403);
        }

        return new JsonResponse([
            'properties' => $modflowModel->soilmodel()->properties(),
            'layers' => $modflowModel->soilmodel()->layers()
        ]);
    }

    /**
     * @Route("/modflowmodels/{id}/soilmodel/layers", name="modflowmodel_soilmodel_layers", methods={"GET"})
     * @param string $id
     * @return JsonResponse
     * @throws PrometheusException
     */
    public function indexSoilmodelLayers(string $id): JsonResponse
    {
        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        /** @var User $user */
        $user = $token->getUser();

        $this->writeMetrics('/modflowmodels');

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        if ($modflowModel->getPermissions($user) === '---') {
            return new JsonResponse([], 403);
        }

        return new JsonResponse($modflowModel->soilmodel()->layers());
    }

    /**
     * @Route("/modflowmodels/{id}/soilmodel/properties", name="modflowmodel_soilmodel_properties", methods={"GET"})
     * @param string $id
     * @return JsonResponse
     * @throws PrometheusException
     */
    public function indexSoilmodelProperties(string $id): JsonResponse
    {
        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        /** @var User $user */
        $user = $token->getUser();

        $this->writeMetrics('/modflowmodels');

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        if ($modflowModel->getPermissions($user) === '---') {
            return new JsonResponse([], 403);
        }

        return new JsonResponse($modflowModel->soilmodel()->properties());
    }

    /**
     * @Route("/modflowmodels/{id}/boundaries", name="modflowmodel_boundaries", methods={"GET"})
     * @param string $id
     * @return JsonResponse
     * @throws PrometheusException
     */
    public function indexBoundaries(string $id): JsonResponse
    {
        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        /** @var User $user */
        $user = $token->getUser();

        $this->writeMetrics('/modflowmodels');

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        if ($modflowModel->getPermissions($user) === '---') {
            return new JsonResponse([], 403);
        }

        return new JsonResponse($modflowModel->boundaries());
    }

    /**
     * @Route("/modflowmodels/{id}/soilmodel/{layerId}", name="modflowmodel_soilmodel_layer", methods={"GET"})
     * @param string $id
     * @param string $layerId
     * @return JsonResponse
     * @throws PrometheusException
     */
    public function indexSoilmodelLayer(string $id, string $layerId): JsonResponse
    {
        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        /** @var User $user */
        $user = $token->getUser();

        $this->writeMetrics('/modflowmodels');

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        if ($modflowModel->getPermissions($user) === '---') {
            return new JsonResponse([], 403);
        }

        /** @var Soilmodel $soilmodel */
        $soilmodel = $modflowModel->soilmodel();

        /** @var Layer $layer */
        $layer = $soilmodel->findLayer($layerId);

        if (!$layer instanceof Layer) {
            return new JsonResponse([], 404);
        }

        return new JsonResponse($layer->toArray());
    }

    /**
     * @Route("/modflowmodels/{id}/boundaries/{bId}", name="modflowmodel_boundary_details", methods={"GET"})
     * @param string $id
     * @param string $bId
     * @return JsonResponse
     * @throws PrometheusException
     * @throws \Exception
     */
    public function indexBoundaryDetails(string $id, string $bId): JsonResponse
    {
        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        /** @var User $user */
        $user = $token->getUser();

        $this->writeMetrics('/modflowmodels');

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        if ($modflowModel->getPermissions($user) === '---') {
            return new JsonResponse([], 403);
        }

        return new JsonResponse($modflowModel->boundaries()->findById($bId));
    }

    /**
     * @Route("/modflowmodels/{id}/calculation", name="modflowmodel_calculation", methods={"GET"})
     * @param string $id
     * @return JsonResponse
     * @throws PrometheusException
     */
    public function indexCalculation(string $id): JsonResponse
    {
        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        /** @var User $user */
        $user = $token->getUser();

        $this->writeMetrics('/modflowmodels');

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        if ($modflowModel->getPermissions($user) === '---') {
            return new JsonResponse([], 403);
        }

        $result = $modflowModel->calculation()->toArray();
        return new JsonResponse($result);
    }

    /**
     * @Route("/modflowmodels/{id}/transport", name="modflowmodel_transport", methods={"GET"})
     * @param string $id
     * @return JsonResponse
     * @throws PrometheusException
     */
    public function indexTransport(string $id): JsonResponse
    {

        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        /** @var User $user */
        $user = $token->getUser();

        $this->writeMetrics('/modflowmodels');

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        if ($modflowModel->getPermissions($user) === '---') {
            return new JsonResponse([], 403);
        }

        $result = $modflowModel->transport()->toArray();
        return new JsonResponse($result);
    }

    /**
     * @Route("/modflowmodels/{id}/variableDensity", name="modflowmodel_variable_density", methods={"GET"})
     * @param string $id
     * @return JsonResponse
     * @throws PrometheusException
     */
    public function indexVariableDensity(string $id): JsonResponse
    {

        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        /** @var User $user */
        $user = $token->getUser();

        $this->writeMetrics('/modflowmodels');

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        if ($modflowModel->getPermissions($user) === '---') {
            return new JsonResponse([], 403);
        }

        $result = $modflowModel->variableDensity()->toArray();
        return new JsonResponse($result);
    }

    /**
     * @Route("/modflowmodels/{id}/packages", name="modflowmodel_packages", methods={"GET"})
     * @param string $id
     * @return JsonResponse
     * @throws PrometheusException
     */
    public function indexPackages(string $id): JsonResponse
    {
        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        /** @var User $user */
        $user = $token->getUser();

        $this->writeMetrics('/modflowmodels');

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        if ($modflowModel->getPermissions($user) === '---') {
            return new JsonResponse([], 403);
        }

        $result = $modflowModel->packages()->toArray();
        return new JsonResponse($result);
    }

    /**
     * @param string $route
     * @throws PrometheusException
     */
    private function writeMetrics(string $route): void
    {
        $metric = $this->collectorRegistry->getCounter('http_requests_total');
        $metric->inc(1, ['handler' => $route]);
    }
}
