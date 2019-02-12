<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\UpdateToolInstanceCommand;
use App\Model\ToolInstance;
use Doctrine\ORM\EntityManagerInterface;

class UpdateToolInstanceCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param UpdateToolInstanceCommand $command
     * @throws \Exception
     */
    public function __invoke(UpdateToolInstanceCommand $command)
    {
        $userId = $command->metadata()['user_id'];
        $id = $command->id();

        /** @var ToolInstance $toolInstance */
        $toolInstance = $this->entityManager->getRepository(ToolInstance::class)->findOneBy(['id' => $id]);

        if (!$toolInstance instanceof ToolInstance) {
            throw new \Exception('ToolInstance not found');
        }

        if ($toolInstance->getUserId() !== $userId) {
            throw new \Exception('The tool cannot be updated due to permission problems.');
        }

        $toolInstance->setMetadata($command->toolMetadata());
        $toolInstance->setData($command->data());
        $this->entityManager->persist($toolInstance);
        $this->entityManager->flush();
    }
}
