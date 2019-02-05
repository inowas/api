<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Domain\ToolInstance\Command\CloneToolInstanceCommand;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCloned;
use App\Domain\ToolInstance\Projection\ToolInstanceProjector;
use App\Repository\AggregateRepository;

class CloneToolInstanceCommandHandler
{
    /** @var AggregateRepository */
    private $aggregateRepository;

    /** @var ToolInstanceProjector */
    private $toolInstanceProjector;


    public function __construct(AggregateRepository $aggregateRepository, ToolInstanceProjector $toolInstanceProjector)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->toolInstanceProjector = $toolInstanceProjector;
    }

    /**
     * @param CloneToolInstanceCommand $command
     * @throws \Exception
     */
    public function __invoke(CloneToolInstanceCommand $command)
    {

        $userId = $command->metadata()['user_id'];
        $baseId = $command->baseId();
        $newId = $command->id();

        $aggregateId = $newId;
        $event = ToolInstanceHasBeenCloned::fromParams($userId, $newId, $baseId);
        $aggregate = ToolInstanceAggregate::withId($aggregateId);
        $aggregate->apply($event);

        $this->aggregateRepository->storeEvent($event);
        $this->toolInstanceProjector->apply($event);
    }
}
