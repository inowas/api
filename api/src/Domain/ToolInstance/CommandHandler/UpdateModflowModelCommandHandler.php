<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Domain\ToolInstance\Command\CreateModflowModelCommand;
use App\Domain\ToolInstance\Command\UpdateModflowModelCommand;
use App\Domain\ToolInstance\Event\ToolInstanceDataHasBeenUpdated;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCreated;
use App\Domain\ToolInstance\Event\ToolInstanceMetadataHasBeenUpdated;
use App\Domain\ToolInstance\Projection\DashboardProjector;
use App\Domain\ToolInstance\Projection\SimpleToolsProjector;
use App\Model\Modflow\Discretization;
use App\Model\ProjectorCollection;
use App\Model\SimpleToolInstance;
use App\Repository\AggregateRepository;
use Doctrine\ORM\EntityManagerInterface;

class UpdateModflowModelCommandHandler
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
     * @param UpdateModflowModelCommand $command
     * @throws \Exception
     */
    public function __invoke(UpdateModflowModelCommand $command)
    {
        $modelId = $command->id();
        $userId = $command->metadata()['user_id'];

        # Load aggregate
        /** @var ToolInstanceAggregate $aggregate */
        $aggregate = $this->aggregateRepository->findAggregateById(ToolInstanceAggregate::class, $modelId);

        if ($aggregate->userId() !== $userId) {
            throw new \Exception('The Model cannot be updated due to permission problems.');
        }

        $toolInstance = $this->entityManager->getRepository(SimpleToolInstance::class)->findOneBy(['id' => $modelId]);

        if (!$toolInstance instanceof SimpleToolInstance) {
            throw new \Exception('ToolInstance not found.');
        }

        $toolMetadata = $toolInstance->getMetadata();
        $newToolMetadata = $command->toolMetadata();

        if ($toolMetadata->isEqualTo($newToolMetadata)) {
            $diff = $toolMetadata->diff($newToolMetadata);
            $event = ToolInstanceMetadataHasBeenUpdated::fromParams($userId, $modelId, $diff);
            $aggregate->apply($event);
            $this->aggregateRepository->storeEvent($event);
            $this->projectors->getProjector(DashboardProjector::class)->apply($event);
            $this->projectors->getProjector(SimpleToolsProjector::class)->apply($event);
        }

        $actualDiscretization = Discretization::fromArray($toolInstance->getData()['discretization']);
        $discretizationUpdate = $command->discretization()->getDiff($actualDiscretization);

        if ($discretizationUpdate->hasContent()) {
            $data = ['discretization' => $discretizationUpdate->toArray()];
            $event = ToolInstanceDataHasBeenUpdated::fromParams($userId, $modelId, $data, ToolInstanceDataHasBeenUpdated::MERGE_STRATEGY_MERGE);

            # Then the event can be applied
            $aggregate->apply($event);

            # Stored
            $this->aggregateRepository->storeEvent($event);

            # Projected
            $this->projectors->getProjector(DashboardProjector::class)->apply($event);
            $this->projectors->getProjector(SimpleToolsProjector::class)->apply($event);
        }
    }
}