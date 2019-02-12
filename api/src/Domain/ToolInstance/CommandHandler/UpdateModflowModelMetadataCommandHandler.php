<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Domain\ToolInstance\Command\UpdateModflowModelMetadataCommand;
use App\Domain\ToolInstance\Event\ModflowModelMetadataHasBeenUpdated;
use App\Domain\ToolInstance\Projection\DashboardProjector;
use App\Domain\ToolInstance\Projection\ModflowModelProjector;
use App\Model\Modflow\ModflowModel;
use App\Model\ProjectorCollection;
use App\Repository\AggregateRepository;
use Doctrine\ORM\EntityManagerInterface;

final class UpdateModflowModelMetadataCommandHandler
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
     * @param UpdateModflowModelMetadataCommand $command
     * @throws \Exception
     */
    public function __invoke(UpdateModflowModelMetadataCommand $command)
    {
        $modelId = $command->id();
        $userId = $command->metadata()['user_id'];

        # Load aggregate
        /** @var ToolInstanceAggregate $aggregate */
        $aggregate = $this->aggregateRepository->findAggregateById(ToolInstanceAggregate::class, $modelId);

        if ($aggregate->userId() !== $userId) {
            throw new \Exception('The Model cannot be updated due to permission problems.');
        }

        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $modelId]);

        if (!$modflowModel instanceof ModflowModel) {
            throw new \Exception('ModflowModel not found.');
        }

        $metadata = $modflowModel->getMetadata();
        $newMetadata = $command->toolMetadata();

        if ($metadata->isEqualTo($newMetadata)) {
            # Nothing to do here!
            return;
        }

        $event = ModflowModelMetadataHasBeenUpdated::fromParams($userId, ToolInstanceAggregate::class, $newMetadata);
        $aggregate->apply($event);
        $this->aggregateRepository->storeEvent($event);
        $this->projectors->getProjector(DashboardProjector::class)->apply($event);
        $this->projectors->getProjector(ModflowModelProjector::class)->apply($event);
    }
}
