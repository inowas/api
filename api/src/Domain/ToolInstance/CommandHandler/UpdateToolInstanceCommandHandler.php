<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\UpdateToolInstanceCommand;
use App\Model\SimpleTool\SimpleTool;
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

        /** @var SimpleTool $simpleTool */
        $simpleTool = $this->entityManager->getRepository(SimpleTool::class)->findOneBy(['id' => $id]);

        if (!$simpleTool instanceof SimpleTool) {
            throw new \Exception('Tool not found');
        }

        if ($simpleTool->userId() !== $userId) {
            throw new \Exception('The tool cannot be updated due to permission problems.');
        }

        $simpleTool->setMetadata($command->toolMetadata());
        $simpleTool->setData($command->data());
        $this->entityManager->persist($simpleTool);
        $this->entityManager->flush();
    }
}
