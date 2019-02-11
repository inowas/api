<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Domain\ToolInstance\Command\DeleteToolInstanceCommand;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenDeleted;
use App\Domain\ToolInstance\Projection\DashboardProjector;
use App\Domain\ToolInstance\Projection\ToolInstancesProjector;
use App\Model\ProjectorCollection;
use App\Repository\AggregateRepository;

class DeleteToolInstanceCommandHandler
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
     * @param DeleteToolInstanceCommand $command
     * @throws \Exception
     */
    public function __invoke(DeleteToolInstanceCommand $command)
    {
        $userId = $command->metadata()['user_id'];
        $id = $command->id();

        $aggregateId = $id;

        /** @var ToolInstanceAggregate $aggregate */
        $aggregate = $this->aggregateRepository->findAggregateById(ToolInstanceAggregate::class, $aggregateId);
        $event = ToolInstanceHasBeenDeleted::fromParams($userId, $aggregateId);

        if ($aggregate->userId() !== $userId) {
            throw new \Exception('The tool cannot be cloned due to permission problems.');
        }

        # Then the event can be applied
        $aggregate->apply($event);

        # Stored
        $this->aggregateRepository->storeEvent($event);

        # Projected
        $this->projectors->getProjector(DashboardProjector::class)->apply($event);
        $this->projectors->getProjector(ToolInstancesProjector::class)->apply($event);
    }
}
