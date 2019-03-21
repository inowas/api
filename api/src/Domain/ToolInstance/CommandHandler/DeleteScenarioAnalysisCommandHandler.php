<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\DeleteScenarioAnalysisCommand;
use App\Model\SimpleTool\SimpleTool;
use Doctrine\ORM\EntityManagerInterface;

class DeleteScenarioAnalysisCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param DeleteScenarioAnalysisCommand $command
     * @throws \Exception
     */
    public function __invoke(DeleteScenarioAnalysisCommand $command)
    {
        $userId = $command->metadata()['user_id'];
        $id = $command->id();

        $simpleTool = $this->entityManager->getRepository(SimpleTool::class)->findOneBy(['id' => $id]);

        if (!$simpleTool instanceof SimpleTool) {
            throw new \Exception('ToolInstance not found');
        }

        if ($simpleTool->userId() !== $userId) {
            throw new \Exception('The scenarioAnalysis cannot be deleted due to permission problems.');
        }

        $simpleTool->setIsArchived(true);
        $this->entityManager->persist($simpleTool);
        $this->entityManager->flush();
    }
}
