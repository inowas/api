<?php

namespace App\Controller;

use App\Model\Mcda\Mcda;
use App\Model\Modflow\ModflowModel;
use App\Model\SimpleTool\SimpleTool;
use App\Model\User;
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
        $numberOfRegisteredUsers = count($this->entityManager->getRepository(User::class)->findAll());
        $this->collectorRegistry->getGauge('users_registered')->set($numberOfRegisteredUsers);

        $numberOfSimpleTools = count($this->entityManager->getRepository(SimpleTool::class)->findAll());
        $this->collectorRegistry->getGauge('projects_simple_tools')->set($numberOfSimpleTools);

        $numberOfModflowModels = count($this->entityManager->getRepository(ModflowModel::class)->findAll());
        $this->collectorRegistry->getGauge('projects_modflow_models')->set($numberOfModflowModels);

        $numberOfMcda = count($this->entityManager->getRepository(Mcda::class)->findAll());
        $this->collectorRegistry->getGauge('projects_mcda')->set($numberOfMcda);

        $formatter = new TextFormatter();
        return new Response($formatter->format($this->collectorRegistry->collect()), 200, [
            'Content-Type' => $formatter->getMimeType(),
        ]);
    }
}
