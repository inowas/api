<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Domain\ToolInstance\Command\CreateToolInstanceCommand;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCreated;
use App\Domain\ToolInstance\Projection\DashboardProjector;
use App\Domain\ToolInstance\Projection\SimpleToolsProjector;
use App\Model\ProjectorCollection;
use App\Repository\AggregateRepository;

class CreateToolInstanceCommandHandler
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
     * @param CreateToolInstanceCommand $command
     * @throws \Exception
     */
    public function __invoke(CreateToolInstanceCommand $command)
    {
        $userId = $command->metadata()['user_id'];
        $metadata = $command->toolMetadata();

        $id = $command->id();
        $tool = $command->tool();
        $data = $command->data();

        $aggregateId = $id;
        $event = ToolInstanceHasBeenCreated::fromParams($userId, $aggregateId, $tool, $metadata, $data);
        $aggregate = ToolInstanceAggregate::withId($aggregateId);

        # Then the event can be applied
        $aggregate->apply($event);

        # Stored
        $this->aggregateRepository->storeEvent($event);

        # Projected
        $this->projectors->getProjector(DashboardProjector::class)->apply($event);
        $this->projectors->getProjector(SimpleToolsProjector::class)->apply($event);
    }
}
