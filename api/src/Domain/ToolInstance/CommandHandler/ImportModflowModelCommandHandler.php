<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\ImportModflowModelCommand;
use App\Model\Modflow\ModflowModel;
use App\Model\ToolMetadata;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;

class ImportModflowModelCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param ImportModflowModelCommand $command
     * @throws \Exception
     */
    public function __invoke(ImportModflowModelCommand $command)
    {
        $modelId = $command->id();
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $modelId]);
        if ($modflowModel instanceof ModflowModel) {
            throw new \Exception(sprintf('ModflowModel with id: %s is already existing', $modelId));
        }

        $userId = $command->metadata()['user_id'];
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $userId]);
        if (!$user instanceof User) {
            throw new \Exception(sprintf('User with id %s not found.', $userId));
        }

        $modflowModel = ModflowModel::createWithParams($modelId, $user, 'T03', ToolMetadata::fromParams(
            $command->name(), $command->description(), $command->isPublic()));

        $modflowModel->setDiscretization($command->discretization());
        $modflowModel->setSoilmodel($command->soilmodel());
        $modflowModel->setBoundaries($command->boundaries());

        $this->entityManager->persist($modflowModel);
        $this->entityManager->flush();
    }
}
