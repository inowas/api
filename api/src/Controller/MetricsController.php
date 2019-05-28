<?php

namespace App\Controller;

use App\Model\Mcda\Mcda;
use App\Model\Modflow\ModflowModel;
use App\Model\SimpleTool\SimpleTool;
use App\Model\User;
use App\Repository\McdaRepository;
use App\Repository\ModflowModelRepository;
use App\Repository\SimpleToolRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TweedeGolf\PrometheusClient\CollectorRegistry;
use TweedeGolf\PrometheusClient\Format\TextFormatter;
use TweedeGolf\PrometheusClient\PrometheusException;

class MetricsController
{
    /** @var CollectorRegistry */
    private $collectorRegistry;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(CollectorRegistry $collectorRegistry, EntityManagerInterface $entityManager)
    {
        $this->collectorRegistry = $collectorRegistry;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/metrics", name="metrics", methods={"GET"})
     * @return Response
     * @throws PrometheusException
     */
    public function index(): Response
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);
        $this->collectorRegistry->getGauge('users_registered')->set($userRepository->count([]));

        /** @var SimpleToolRepository $simpleToolRepository */
        $simpleToolRepository = $this->entityManager->getRepository(SimpleTool::class);
        $this->collectorRegistry->getGauge('projects_simple_tools')->set($simpleToolRepository->count([]));

        /** @var ModflowModelRepository $modflowModelRepository */
        $modflowModelRepository = $this->entityManager->getRepository(ModflowModel::class);
        $this->collectorRegistry->getGauge('projects_modflow_models')->set($modflowModelRepository->count([]));

        /** @var McdaRepository $mcdaRepository */
        $mcdaRepository = $this->entityManager->getRepository(Mcda::class);
        $this->collectorRegistry->getGauge('projects_mcda')->set($mcdaRepository->count([]));

        $formatter = new TextFormatter();
        return new Response($formatter->format($this->collectorRegistry->collect()), 200, [
            'Content-Type' => $formatter->getMimeType(),
        ]);
    }
}
