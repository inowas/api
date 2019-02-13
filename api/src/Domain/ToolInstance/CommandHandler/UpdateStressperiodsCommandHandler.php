<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\UpdateStressperiodsCommand;
use App\Model\Modflow\ModflowModel;
use App\Model\ProjectorCollection;
use App\Repository\AggregateRepository;
use Doctrine\ORM\EntityManagerInterface;

class UpdateStressperiodsCommandHandler
{
    /** @var AggregateRepository */
    private $aggregateRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ProjectorCollection */
    private $projectors;

    public function __construct(AggregateRepository $aggregateRepository, EntityManagerInterface $entityManager, ProjectorCollection $projectors)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->entityManager = $entityManager;
        $this->projectors = $projectors;
    }

    /**
     * @param UpdateStressperiodsCommand $command
     * @throws \Exception
     */
    public function __invoke(UpdateStressperiodsCommand $command)
    {
        $modelId = $command->id();
        $userId = $command->metadata()['user_id'];

        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $modelId]);

        if (!$modflowModel instanceof ModflowModel) {
            throw new \Exception('ModflowModel not found');
        }

        if ($modflowModel->userId() !== $userId) {
            throw new \Exception('The Model cannot be updated due to permission problems.');
        }

        $discretization = $modflowModel->discretization();
        $discretization->setStressperiods($command->stressperiods());

        $modflowModel->setDiscretization($discretization);
        $this->entityManager->persist($modflowModel);
        $this->entityManager->flush();
    }
}
