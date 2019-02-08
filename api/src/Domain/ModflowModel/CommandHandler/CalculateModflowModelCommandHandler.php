<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\CommandHandler;

use App\Domain\ModflowModel\Aggregate\ModflowModelAggregate;
use App\Domain\ModflowModel\Command\CalculateModflowModelCommand;
use App\Domain\ModflowModel\Event\CalculationHasBeenStarted;
use App\Domain\ModflowModel\Projection\ModflowModelProjector;
use App\Repository\AggregateRepository;

class CalculateModflowModelCommandHandler
{
    /** @var AggregateRepository */
    private $aggregateRepository;

    /** @var ModflowModelProjector */
    private $modflowModelProjector;


    public function __construct(AggregateRepository $aggregateRepository, ModflowModelProjector $modflowModelProjector)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->modflowModelProjector = $modflowModelProjector;
    }

    /**
     * @param CalculateModflowModelCommand $command
     * @throws \Exception
     */
    public function __invoke(CalculateModflowModelCommand $command)
    {
        $modelId = $command->id();
        $userId = $command->metadata()['user_id'];

        /** @var ModflowModelAggregate $modelAggregate */
        $modelAggregate = $this->aggregateRepository->findAggregateById(ModflowModelAggregate::class, $modelId);

        if ($userId === $modelAggregate->userId()) {
            throw new \Exception('The tool cannot be cloned due to permission problems.');
        }

        // Todo
        // send to calculate



        $event = CalculationHasBeenStarted::fromParams($userId, $modelId);
        $modelAggregate->apply($event);

        # Stored
        $this->aggregateRepository->storeEvent($event);

        # Projected
        $this->modflowModelProjector->apply($event);
    }
}
