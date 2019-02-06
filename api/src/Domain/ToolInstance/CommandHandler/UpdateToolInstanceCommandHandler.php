<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Domain\ToolInstance\Command\UpdateToolInstanceCommand;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenUpdated;
use App\Domain\ToolInstance\Projection\ToolInstanceProjector;
use App\Model\ToolInstance;
use App\Repository\AggregateRepository;
use Doctrine\ORM\EntityManagerInterface;

class UpdateToolInstanceCommandHandler
{
    /** @var AggregateRepository */
    private $aggregateRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ToolInstanceProjector */
    private $toolInstanceProjector;


    public function __construct(AggregateRepository $aggregateRepository, ToolInstanceProjector $toolInstanceProjector, EntityManagerInterface $entityManager)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->entityManager = $entityManager;
        $this->toolInstanceProjector = $toolInstanceProjector;
    }

    /**
     * @param UpdateToolInstanceCommand $command
     * @throws \Exception
     */
    public function __invoke(UpdateToolInstanceCommand $command)
    {
        $userId = $command->metadata()['user_id'];
        $id = $command->id();

        $toolInstanceFromProjection = $this->entityManager->getRepository(ToolInstance::class)->findOneBy(['id' => $id]);

        if (!$toolInstanceFromProjection instanceof ToolInstance) {
            throw new \Exception(sprintf('ToolInstance with id: %s not found in Projection.', $id));
        }

        $name = $command->name() === $toolInstanceFromProjection->getName() ? null : $command->name();
        $description = $command->description() === $toolInstanceFromProjection->getDescription() ? null : $command->description();
        $isPublic = $command->isPublic() === $toolInstanceFromProjection->isPublic() ? null : $command->isPublic();
        $data = $command->data() == $toolInstanceFromProjection->getData() ? null : $command->data();

        $aggregateId = $id;
        /** @var ToolInstanceAggregate $aggregate */
        $aggregate = $this->aggregateRepository->findAggregateById($aggregateId);
        $event = ToolInstanceHasBeenUpdated::fromParams($userId, $aggregateId, $name, $description, $isPublic, $data);

        if ($aggregate->userId() !== $userId) {
            throw new \Exception('The tool cannot be cloned due to permission problems.');
        }

        $aggregate->apply($event);

        $this->aggregateRepository->storeEvent($event);
        $this->toolInstanceProjector->apply($event);
    }
}
