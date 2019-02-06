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
        $originId = $command->baseId();
        $cloneId = $command->id();

        # Get the original toolInstance
        /** @var ToolInstanceAggregate $original */
        $original = $this->aggregateRepository->findAggregateById($originId);

        # The user needs to be the owner of the model or the model has to be public
        $canBeCloned = ($userId === $original->userId() || true === $original->isPublic());
        if (!$canBeCloned) {
            throw new \Exception('The tool cannot be cloned due to permission problems.');
        }

        $aggregateId = $cloneId;
        $aggregate = ToolInstanceAggregate::withId($aggregateId);
        $event = ToolInstanceHasBeenCloned::fromParams($userId, $aggregateId, $baseId, $original->isPublic());

        # Then the event can be applied
        $aggregate->apply($event);

        # Stored
        $this->aggregateRepository->storeEvent($event);

        # Projected
        $this->toolInstanceProjector->apply($event);
    }
}
