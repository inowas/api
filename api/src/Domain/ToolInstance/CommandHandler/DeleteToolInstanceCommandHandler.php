<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\DeleteToolInstanceCommand;
use App\Model\Modflow\ModflowModel;
use App\Model\SimpleTool\SimpleTool;
use App\Model\ToolInstance;
use Doctrine\ORM\EntityManagerInterface;

class DeleteToolInstanceCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param DeleteToolInstanceCommand $command
     * @throws \Exception
     */
    public function __invoke(DeleteToolInstanceCommand $command)
    {
        $userId = $command->metadata()['user_id'];
        $id = $command->id();


        $toolInstance = $this->entityManager->getRepository(SimpleTool::class)->findOneBy(['id' => $id]);

        if (null === $toolInstance) {
            $toolInstance = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $id]);
        }

        if (!$toolInstance instanceof ToolInstance) {
            throw new \Exception('ToolInstance not found');
        }

        if ($toolInstance->userId() !== $userId) {
            throw new \Exception('The tool cannot be deleted due to permission problems.');
        }

        $toolInstance->setArchived(true);
        $this->entityManager->persist($toolInstance);
        $this->entityManager->flush();
    }
}
