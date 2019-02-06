<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Domain\ToolInstance\Command\DeleteToolInstanceCommand;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenDeleted;
use App\Domain\ToolInstance\Projection\ToolInstanceProjector;
use App\Repository\AggregateRepository;

class DeleteToolInstanceCommandHandler
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
     * @param DeleteToolInstanceCommand $command
     * @throws \Exception
     */
    public function __invoke(DeleteToolInstanceCommand $command)
    {
        $userId = $command->metadata()['user_id'];
        $id = $command->id();

        $aggregateId = $id;

        /** @var ToolInstanceAggregate $aggregate */
        $aggregate = $this->aggregateRepository->findAggregateById($aggregateId);
        $event = ToolInstanceHasBeenDeleted::fromParams($userId, $aggregateId);

        if ($aggregate->userId() !== $userId) {
            throw new \Exception('The tool cannot be cloned due to permission problems.');
        }

        $aggregate->apply($event);

        $this->aggregateRepository->storeEvent($event);
        $this->toolInstanceProjector->apply($event);
    }
}
