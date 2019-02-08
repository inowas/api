<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\CommandHandler;

use App\Domain\ModflowModel\Aggregate\ModflowModelAggregate;
use App\Domain\ModflowModel\Command\DeleteModflowModelCommand;
use App\Domain\ModflowModel\Command\UpdateModflowModelCommand;
use App\Domain\ModflowModel\Event\ModflowModelHasBeenDeleted;
use App\Domain\ModflowModel\Event\ModflowModelHasBeenUpdated;
use App\Domain\ModflowModel\Projection\ModflowModelProjector;
use App\Domain\ToolInstance\Command\DeleteToolInstanceCommand;
use App\Domain\ToolInstance\Command\UpdateToolInstanceCommand;
use App\Repository\AggregateRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteModflowModelCommandHandler
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
     * @param DeleteModflowModelCommand $command
     * @throws \Exception
     */
    public function __invoke(DeleteModflowModelCommand $command)
    {
        $modelId = $command->id();
        $userId = $command->metadata()['user_id'];

        /** @var ModflowModelAggregate $modflowModelAggregate */
        $modflowModelAggregate = $this->aggregateRepository->findAggregateById(ModflowModelAggregate::class, $modelId);

        if ($modflowModelAggregate->userId() !== $userId) {
            throw new \Exception('The tool cannot be cloned due to permission problems.');
        }

        $event = ModflowModelHasBeenDeleted::fromParams($userId, $modelId);
        $modflowModelAggregate->apply($event);
        $this->aggregateRepository->storeEvent($event);
        $this->modflowModelProjector->apply($event);

        # Send command to update a toolInstance
        $command = DeleteToolInstanceCommand::fromParams($modelId);
        $command->withAddedMetadata('user_id', $userId);
        $this->messageBus->dispatch($command);
    }
}
