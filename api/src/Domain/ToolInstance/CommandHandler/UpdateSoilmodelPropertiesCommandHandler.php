<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\UpdateSoilmodelPropertiesCommand;
use App\Model\Modflow\ModflowModel;
use Doctrine\ORM\EntityManagerInterface;

final class UpdateSoilmodelPropertiesCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param UpdateSoilmodelPropertiesCommand $command
     * @throws \Exception
     */
    public function __invoke(UpdateSoilmodelPropertiesCommand $command)
    {
        $modelId = $command->id();
        $userId = $command->metadata()['user_id'];

        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $modelId]);

        if (!$modflowModel instanceof ModflowModel) {
            throw new \Exception('ModflowModel not found');
        }

        if ($modflowModel->userId() !== $userId) {
            throw new \Exception('The Model cannot be updated due to permission problems.');
        }

        $soilmodel = $modflowModel->soilmodel();
        $soilmodel->updateProperties($command->properties());
        $modflowModel->setSoilmodel($soilmodel);

        $this->entityManager->persist($modflowModel);
        $this->entityManager->flush();
    }
}
