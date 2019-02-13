<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Domain\ToolInstance\Command\CreateToolInstanceCommand;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCreated;
use App\Domain\ToolInstance\Projection\DashboardProjector;
use App\Domain\ToolInstance\Projection\ToolInstanceProjector;
use App\Model\ProjectorCollection;
use App\Model\SimpleTool\SimpleTool;
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
        $id = $command->id();
        $userId = $command->metadata()['user_id'];
        $tool = $command->tool();
        $metadata = $command->toolMetadata();
        $data = $command->data();

        $simpleTool = SimpleTool::createWithParams($id, $userId, $tool, $metadata);
        $simpleTool->setData($data);
        $this->entityManager->persist($simpleTool);
        $this->entityManager->flush();
    }
}
