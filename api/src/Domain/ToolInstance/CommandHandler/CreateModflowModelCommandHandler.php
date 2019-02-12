<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Domain\ToolInstance\Command\CreateModflowModelCommand;
use App\Domain\ToolInstance\Event\ModflowModelHasBeenCreated;
use App\Domain\ToolInstance\Projection\DashboardProjector;
use App\Domain\ToolInstance\Projection\ModflowModelProjector;
use App\Model\Modflow\ModflowModel;
use App\Model\ProjectorCollection;
use App\Repository\AggregateRepository;

class CreateModflowModelCommandHandler
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
     * @param CreateModflowModelCommand $command
     * @throws \Exception
     */
    public function __invoke(CreateModflowModelCommand $command)
    {
        $modelId = $command->id();
        $userId = $command->metadata()['user_id'];
        $toolMetadata = $command->toolMetadata();
        $discretization = $command->discretization();

        $modflowModel = ModflowModel::fromParams($modelId, $userId);
        $modflowModel->setMetadata($toolMetadata);
        $modflowModel->setDiscretization($discretization);


        # Create ModflowModel
        $aggregate = ToolInstanceAggregate::withId($modelId);
        $event = ModflowModelHasBeenCreated::fromParams($userId, $modelId, $modflowModel);

        # Then the event can be applied
        $aggregate->apply($event);

        # Stored
        $this->aggregateRepository->storeEvent($event);

        # Projected
        $this->projectors->getProjector(DashboardProjector::class)->apply($event);
        $this->projectors->getProjector(ModflowModelProjector::class)->apply($event);
    }
}
