<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\CreateScenarioCommand;
use App\Model\Modflow\ModflowModel;
use App\Model\ScenarioAnalysis\ScenarioAnalysis;
use App\Model\SimpleTool\SimpleTool;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use RuntimeException;

class CreateScenarioCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param CreateScenarioCommand $command
     * @throws Exception
     */
    public function __invoke(CreateScenarioCommand $command)
    {
        $id = $command->id();
        $userId = $command->metadata()['user_id'];

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $userId]);
        if (!$user instanceof User) {
            throw new RuntimeException(sprintf('User with id %s not found.', $userId));
        }

        $simpleTool = $this->entityManager->getRepository(SimpleTool::class)->findOneBy(['id' => $id]);
        if (!$simpleTool instanceof SimpleTool) {
            throw new RuntimeException('ToolInstance not found');
        }

        if ($simpleTool->userId() !== $userId) {
            throw new RuntimeException('The scenarioAnalysis cannot be deleted due to permission problems.');
        }


        $this->cloneModel($command->basemodelId(), $command->scenarioId(), $user);

        $scenarioAnalysis = ScenarioAnalysis::fromArray($simpleTool->data());
        $scenarioAnalysis->addScenarioId($command->scenarioId());

        $simpleTool->setData($scenarioAnalysis->toArray());

        $this->entityManager->persist($simpleTool);
        $this->entityManager->flush();
    }

    /**
     * @param $modelId
     * @param $newModelId
     * @param $user
     * @throws Exception
     */
    private function cloneModel($modelId, $newModelId, $user): void
    {
        $modelToClone = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $modelId]);

        if (!$modelToClone instanceof ModflowModel) {
            throw new RuntimeException('Model not found.');
        }

        $clonedModel = clone $modelToClone;
        $clonedModel->setId($newModelId);
        $clonedModel->setIsScenario(true);
        $clonedModel->setUser($user);
        $name = $clonedModel->name();
        $name .= ' (clone)';
        $clonedModel->setName($name);
        $this->entityManager->persist($clonedModel);
        $this->entityManager->flush();
    }
}
