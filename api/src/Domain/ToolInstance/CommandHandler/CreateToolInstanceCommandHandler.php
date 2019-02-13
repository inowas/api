<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\CreateToolInstanceCommand;
use App\Model\SimpleTool\SimpleTool;
use Doctrine\ORM\EntityManagerInterface;

class CreateToolInstanceCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param CreateToolInstanceCommand $command
     * @throws \Exception
     */
    public function __invoke(CreateToolInstanceCommand $command)
    {
        $id = $command->id();
        $userId = $command->metadata()['user_id'];
        $tool = $command->tool();
        $metadata = $command->toolMetadata();
        $data = $command->data();

        $simpleTool = SimpleTool::createWithParams($id, $userId, $tool, $metadata);
        $simpleTool->setData($data);
        $this->entityManager->persist($simpleTool);
        $this->entityManager->flush();
    }
}
