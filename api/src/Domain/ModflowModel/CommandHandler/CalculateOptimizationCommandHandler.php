<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\CommandHandler;

use App\Domain\ModflowModel\Aggregate\ModflowModelAggregate;
use App\Domain\ModflowModel\Command\CalculateModflowModelCommand;
use App\Domain\ModflowModel\Command\CalculateOptimizationCommand;
use App\Domain\ModflowModel\Event\CalculationStateHasBeenChanged;
use App\Domain\ModflowModel\Event\OptimizationStateHasBeenChanged;
use App\Domain\ModflowModel\Projection\ModflowModelProjector;
use App\Repository\AggregateRepository;

class CalculateOptimizationCommandHandler
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
     * @param CalculateOptimizationCommand $command
     * @throws \Exception
     */
    public function __invoke(CalculateOptimizationCommand $command)
    {
        $modelId = $command->id();
        $optimizationId = $command->optimizationId();
        $isInitial = $command->isInitial();
        $userId = $command->metadata()['user_id'];

        /** @var ModflowModelAggregate $modelAggregate */
        $modelAggregate = $this->aggregateRepository->findAggregateById(ModflowModelAggregate::class, $modelId);

        if ($userId === $modelAggregate->userId()) {
            throw new \Exception('The tool cannot be cloned due to permission problems.');
        }

        # Todo
        # send to optimize

        # Todo define calculation
        $optimization = [];

        $event = OptimizationStateHasBeenChanged::fromParams($userId, $modelId, $optimization);
        $modelAggregate->apply($event);

        # Stored
        $this->aggregateRepository->storeEvent($event);

        # Projected
        $this->modflowModelProjector->apply($event);
    }
}
