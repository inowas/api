<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Domain\ToolInstance\Command\CreateToolInstanceCommand;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCreated;
use App\Domain\ToolInstance\Projector\ToolInstanceProjector;
use App\Repository\AggregateRepository;

class CreateToolInstanceCommandHandler
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
     * @param CreateToolInstanceCommand $command
     * @throws \Exception
     */
    public function __invoke(CreateToolInstanceCommand $command)
    {

        $userId = $command->metadata()['user_id'];

        $id = $command->id();
        $tool = $command->tool();
        $name = $command->name();
        $description = $command->description();
        $isPublic = $command->isPublic();
        $data = $command->data();


        $aggregateId = $id;
        $event = ToolInstanceHasBeenCreated::fromParams($userId, $aggregateId, $tool, $name, $description, $isPublic, $data);
        $aggregate = ToolInstanceAggregate::withId($aggregateId);
        $aggregate->apply($event);

        $this->aggregateRepository->storeEvent($event);
        $this->toolInstanceProjector->apply($event);
    }
}
