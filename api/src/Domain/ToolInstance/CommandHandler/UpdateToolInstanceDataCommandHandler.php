<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Domain\ToolInstance\Command\UpdateToolInstanceDataCommand;
use App\Domain\ToolInstance\Event\ToolInstanceDataHasBeenUpdated;
use App\Domain\ToolInstance\Projection\SimpleToolsProjector;
use App\Model\ProjectorCollection;
use App\Repository\AggregateRepository;

class UpdateToolInstanceDataCommandHandler
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
     * @param UpdateToolInstanceDataCommand $command
     * @throws \Exception
     */
    public function __invoke(UpdateToolInstanceDataCommand $command)
    {
        $userId = $command->metadata()['user_id'];
        $aggregateId = $command->id();

        /** @var ToolInstanceAggregate $aggregate */
        $aggregate = $this->aggregateRepository->findAggregateById(ToolInstanceAggregate::class, $aggregateId);

        if ($aggregate->userId() !== $userId) {
            throw new \Exception('The tool cannot be updated due to permission problems.');
        }

        $event = ToolInstanceDataHasBeenUpdated::fromParams($userId, $aggregateId, $command->data());

        # Then the event can be applied
        $aggregate->apply($event);

        # Stored
        $this->aggregateRepository->storeEvent($event);

        # Projected
        $this->projectors->getProjector(SimpleToolsProjector::class)->apply($event);
    }
}
