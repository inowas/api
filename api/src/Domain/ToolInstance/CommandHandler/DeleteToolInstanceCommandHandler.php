<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\DeleteToolInstanceCommand;
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

        /** @var ToolInstance $toolInstance */
        $toolInstance = $this->entityManager->getRepository(ToolInstance::class)->findOneBy(['id' => $id]);
        if ($toolInstance->getUserId() !== $userId) {
            throw new \Exception('The tool cannot be cloned due to permission problems.');
        }

        $this->entityManager->remove($toolInstance);
        $this->entityManager->flush();
    }
}
