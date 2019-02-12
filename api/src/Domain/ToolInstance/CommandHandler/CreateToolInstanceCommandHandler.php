<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Domain\ToolInstance\Command\CreateToolInstanceCommand;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCreated;
use App\Domain\ToolInstance\Projection\DashboardProjector;
use App\Domain\ToolInstance\Projection\ToolInstanceProjector;
use App\Model\ProjectorCollection;
use App\Model\ToolInstance;
use App\Repository\AggregateRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class CreateToolInstanceCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param CreateToolInstanceCommand $command
     * @throws \Exception
     */
    public function __invoke(CreateToolInstanceCommand $command)
    {
        $userId = $command->metadata()['user_id'];
        $metadata = $command->toolMetadata();

        $id = $command->id();
        $tool = $command->tool();
        $data = $command->data();

        $toolInstance = ToolInstance::createWith($id, $tool);
        $toolInstance->setUserId($userId);
        $toolInstance->setMetadata($metadata);
        $toolInstance->setData($data);

        $this->entityManager->persist($toolInstance);
        $this->entityManager->flush();
    }
}
