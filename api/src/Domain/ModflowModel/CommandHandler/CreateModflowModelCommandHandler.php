<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\CommandHandler;

use App\Domain\ModflowModel\Aggregate\ModflowModelAggregate;
use App\Domain\ModflowModel\Command\CreateModflowModelCommand;
use App\Domain\ModflowModel\Event\ModflowModelHasBeenCreated;
use App\Domain\ModflowModel\Projection\ModflowModelProjector;
use App\Domain\ToolInstance\Command\CreateToolInstanceCommand;
use App\Repository\AggregateRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateModflowModelCommandHandler
{
    /** @var AggregateRepository */
    private $aggregateRepository;

    /** @var ModflowModelProjector */
    private $modflowModelProjector;

    /** @var MessageBusInterface */
    private $messageBus;


    public function __construct(AggregateRepository $aggregateRepository, ModflowModelProjector $modflowModelProjector, MessageBusInterface $bus)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->modflowModelProjector = $modflowModelProjector;
        $this->messageBus = $bus;
    }

    /**
     * @param CreateModflowModelCommand $command
     * @throws \Exception
     */
    public function __invoke(CreateModflowModelCommand $command)
    {
        $modelId = $command->id();
        $userId = $command->metadata()['user_id'];
        $name = $command->name();
        $description = $command->description();
        $isPublic = $command->isPublic();
        $discretization = $command->discretization();

        # Create ModflowModel
        $event = ModflowModelHasBeenCreated::fromParams($userId, $modelId, $name, $description, $isPublic, $discretization);
        $modelAggregate = ModflowModelAggregate::withId($modelId);
        $modelAggregate->apply($event);
        $this->aggregateRepository->storeEvent($event);
        $this->modflowModelProjector->apply($event);

        # Send command to create a toolInstance
        $command = CreateToolInstanceCommand::fromParams($modelId, 'T03', $name, $description, $isPublic, []);
        $command->withAddedMetadata('user_id', $userId);
        $this->messageBus->dispatch($command);
    }
}
