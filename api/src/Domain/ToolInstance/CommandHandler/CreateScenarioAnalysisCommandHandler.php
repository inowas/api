<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\CreateScenarioAnalysisCommand;
use App\Model\ScenarioAnalysis\ScenarioAnalysis;
use App\Model\SimpleTool\SimpleTool;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;

class CreateScenarioAnalysisCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param CreateScenarioAnalysisCommand $command
     * @throws \Exception
     */
    public function __invoke(CreateScenarioAnalysisCommand $command)
    {
        $id = $command->id();
        $userId = $command->metadata()['user_id'];
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $userId]);

        if (!$user instanceof User) {
            throw new \Exception(sprintf('User with id %s not found.', $userId));
        }

        $tool = 'T07';
        $metadata = $command->toolMetadata();
        $instance = SimpleTool::createWithParams($id, $user, $tool, $metadata);

        $scenarioAnalysis = ScenarioAnalysis::createWithBaseId($command->basemodelId());

        $instance->setData($scenarioAnalysis->toArray());
        $this->entityManager->persist($instance);
        $this->entityManager->flush();
    }
}
