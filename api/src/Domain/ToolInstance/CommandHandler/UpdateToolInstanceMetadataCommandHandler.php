<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Domain\ToolInstance\Command\UpdateToolInstanceMetadataCommand;
use App\Domain\ToolInstance\Event\ToolInstanceMetadataHasBeenUpdated;
use App\Domain\ToolInstance\Projection\DashboardProjector;
use App\Domain\ToolInstance\Projection\SimpleToolsProjector;
use App\Model\ProjectorCollection;
use App\Repository\AggregateRepository;

class UpdateToolInstanceMetadataCommandHandler
{
    /** @var AggregateRepository */
    private $aggregateRepository;

    /** @var ProjectorCollection */
    private $projectors;


    public function __construct(AggregateRepository $aggregateRepository, ProjectorCollection $projectors)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->projectors = $projectors;
    }

    /**
     * @param UpdateToolInstanceMetadataCommand $command
     * @throws \Exception
     */
    public function __invoke(UpdateToolInstanceMetadataCommand $command)
    {
        $userId = $command->metadata()['user_id'];
        $aggregateId = $command->id();


        /** @var ToolInstanceAggregate $aggregate */
        $aggregate = $this->aggregateRepository->findAggregateById(ToolInstanceAggregate::class, $aggregateId);

        if ($aggregate->userId() !== $userId) {
            throw new \Exception('The tool cannot be cloned due to permission problems.');
        }

        # Then the event can be applied
        $event = ToolInstanceMetadataHasBeenUpdated::fromParams($userId, $aggregateId, $command->toolMetadata());
        $aggregate->apply($event);

        # Stored
        $this->aggregateRepository->storeEvent($event);

        # Projected
        $this->projectors->getProjector(DashboardProjector::class)->apply($event);
        $this->projectors->getProjector(SimpleToolsProjector::class)->apply($event);
    }
}
