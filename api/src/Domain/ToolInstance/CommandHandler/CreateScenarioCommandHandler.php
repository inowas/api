<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\CreateScenarioCommand;
use App\Model\Modflow\ModflowModel;
use App\Model\ScenarioAnalysis\ScenarioAnalysis;
use App\Model\SimpleTool\SimpleTool;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;

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
     * @throws \Exception
     */
    public function __invoke(CreateScenarioCommand $command)
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

        if ($simpleTool->userId() !== $userId) {
            throw new \Exception('The scenarioAnalysis cannot be deleted due to permission problems.');
        }


        $this->cloneModel($command->basemodelId(), $command->scenarioId(), $user);

        $scenarioAnalysis = ScenarioAnalysis::fromArray($simpleTool->data());
        $scenarioAnalysis->addScenarioId($command->scenarioId());

        $simpleTool->setData($scenarioAnalysis->toArray());

        $this->entityManager->persist($simpleTool);
        $this->entityManager->flush();

        //$newSimpleTool = $this->entityManager->getRepository(SimpleTool::class)->findOneBy(['id' => $id]);
        //return new JsonResponse(var_dump($simpleTool->toArray()));
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
        $name = $clonedModel->name();
        $name = $name." (clone)";
        $clonedModel->setName($name);
        $this->entityManager->persist($clonedModel);
        $this->entityManager->flush();
    }
}
