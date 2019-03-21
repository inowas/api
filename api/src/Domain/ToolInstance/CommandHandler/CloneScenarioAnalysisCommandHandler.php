<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\CloneScenarioAnalysisCommand;
use App\Model\Modflow\ModflowModel;
use App\Model\ScenarioAnalysis\ScenarioAnalysis;
use App\Model\SimpleTool\SimpleTool;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;

class CloneScenarioAnalysisCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param CloneScenarioAnalysisCommand $command
     * @throws \Exception
     */
    public function __invoke(CloneScenarioAnalysisCommand $command)
    {
        $id = $command->id();
        $userId = $command->metadata()['user_id'];

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $userId]);
        if (!$user instanceof User) {
            throw new \Exception(sprintf('User with id %s not found.', $userId));
        }

        $simpleTool = $this->entityManager->getRepository(SimpleTool::class)->findOneBy(['id' => $id]);
        if (!$simpleTool instanceof SimpleTool) {
            throw new \Exception('ToolInstance not found');
        }

        $oldScenarioAnalysis = ScenarioAnalysis::fromArray($simpleTool->data());
        $newScenarioAnalysis = clone $oldScenarioAnalysis;

        $this->cloneModel($oldScenarioAnalysis->baseId(), $newScenarioAnalysis->baseId(), $user);
        foreach ($oldScenarioAnalysis->scenarioIds() as $key => $oldScenarioId) {
            $this->cloneModel($oldScenarioAnalysis->scenarioIds()[$key], $newScenarioAnalysis->scenarioIds()[$key], $user);
        }

        $newSimpleTool = clone $simpleTool;
        $newSimpleTool->setId($command->newId());
        $newSimpleTool->setUser($user);
        $newSimpleTool->setData($newScenarioAnalysis->toArray());

        $name = $newSimpleTool->name();
        $name .= ' (clone)';
        $newSimpleTool->setName($name);

        $this->entityManager->persist($newSimpleTool);
        $this->entityManager->flush();
    }

    /**
     * @param $modelId
     * @param $newModelId
     * @param $user
     * @throws \Exception
     */
    private function cloneModel($modelId, $newModelId, $user): void
    {
        $modelToClone = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $modelId]);

        if (!$modelToClone instanceof ModflowModel) {
            throw new \Exception('Model not found.');
        }

        $clonedModel = clone $modelToClone;
        $clonedModel->setId($newModelId);
        $clonedModel->setIsScenario(true);
        $clonedModel->setUser($user);
        $this->entityManager->persist($clonedModel);
        $this->entityManager->flush();
    }
}
