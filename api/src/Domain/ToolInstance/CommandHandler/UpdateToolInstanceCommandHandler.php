<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Domain\ToolInstance\Command\UpdateToolInstanceCommand;
use App\Domain\ToolInstance\Event\ToolInstanceDataHasBeenUpdated;
use App\Domain\ToolInstance\Event\ToolInstanceMetadataHasBeenUpdated;
use App\Domain\ToolInstance\Projection\DashboardProjector;
use App\Domain\ToolInstance\Projection\ToolInstancesProjector;
use App\Model\ProjectorCollection;
use App\Repository\AggregateRepository;

class UpdateToolInstanceCommandHandler
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
     * @param UpdateToolInstanceCommand $command
     * @throws \Exception
     */
    public function __invoke(UpdateToolInstanceCommand $command)
    {
        $userId = $command->metadata()['user_id'];
        $aggregateId = $command->id();

        /** @var ToolInstanceAggregate $aggregate */
        $aggregate = $this->aggregateRepository->findAggregateById(ToolInstanceAggregate::class, $aggregateId);

        if ($aggregate->userId() !== $userId) {
            throw new \Exception('The tool cannot be updated due to permission problems.');
        }

        $metadata = $command->toolMetadata();
        $event = ToolInstanceMetadataHasBeenUpdated::fromParams($userId, $aggregateId, $metadata);
        $aggregate->apply($event);
        $this->aggregateRepository->storeEvent($event);
        $this->projectors->getProjector(DashboardProjector::class)->apply($event);
        $this->projectors->getProjector(ToolInstancesProjector::class)->apply($event);


        if ($command->data()) {
            $event = ToolInstanceDataHasBeenUpdated::fromParams($userId, $aggregateId, $command->data());
            $aggregate->apply($event);
            $this->aggregateRepository->storeEvent($event);
            $this->projectors->getProjector(ToolInstancesProjector::class)->apply($event);
        }
    }
}
