<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\DeleteModflowModelCommand;
use App\Model\Modflow\ModflowModel;
use Doctrine\ORM\EntityManagerInterface;

class DeleteModflowModelCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param DeleteModflowModelCommand $command
     * @throws \Exception
     */
    public function __invoke(DeleteModflowModelCommand $command)
    {
        $userId = $command->metadata()['user_id'];
        $id = $command->id();

        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);

        if (!$modflowModel instanceof ModflowModel) {
            throw new \Exception('ToolInstance not found');
        }

        if ($modflowModel->userId() !== $userId) {
            throw new \Exception('The modflowModel cannot be deleted due to permission problems.');
        }

        $modflowModel->setIsArchived(true);
        $this->entityManager->persist($modflowModel);
        $this->entityManager->flush();
    }
}
