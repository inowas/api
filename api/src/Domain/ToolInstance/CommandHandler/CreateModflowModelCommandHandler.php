<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\CreateModflowModelCommand;
use App\Model\Modflow\ModflowModel;
use App\Model\ToolInstance;
use Doctrine\ORM\EntityManagerInterface;

class CreateModflowModelCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param CreateModflowModelCommand $command
     * @throws \Exception
     */
    public function __invoke(CreateModflowModelCommand $command)
    {
        $modelId = $command->id();
        $userId = $command->metadata()['user_id'];
        $toolMetadata = $command->toolMetadata();
        $discretization = $command->discretization();

        $modflowModel = ModflowModel::create();
        $modflowModel->setDiscretization($discretization);

        $toolInstance = ToolInstance::createWith($modelId, 'T03');
        $toolInstance->setUserId($userId);
        $toolInstance->setMetadata($toolMetadata);
        $toolInstance->setData($modflowModel->toArray());

        $this->entityManager->persist($toolInstance);
        $this->entityManager->flush();
    }
}
