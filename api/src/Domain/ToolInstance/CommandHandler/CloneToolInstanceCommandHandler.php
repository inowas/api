<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\CloneToolInstanceCommand;
use App\Model\Modflow\ModflowModel;
use App\Model\SimpleTool\SimpleTool;
use App\Model\ToolInstance;
use Doctrine\ORM\EntityManagerInterface;

class CloneToolInstanceCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param CloneToolInstanceCommand $command
     * @throws \Exception
     */
    public function __invoke(CloneToolInstanceCommand $command)
    {
        $userId = $command->metadata()['user_id'];
        $originId = $command->baseId();
        $cloneId = $command->id();

        /** @var SimpleTool $original */
        $original = $this->entityManager->getRepository(SimpleTool::class)->findOneBy(['id' => $originId]);

        if (null === $original) {
            $original = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $originId]);
        }

        if (!$original instanceof ToolInstance) {
            throw new \Exception('ToolInstance not found');
        }

        # The user needs to be the owner of the model or the model has to be public
        $canBeCloned = ($userId === $original->userId() || true === $original->isPublic());
        if (!$canBeCloned) {
            throw new \Exception('The tool cannot be cloned due to permission problems.');
        }

        /** @var ToolInstance $clone */
        $clone = clone $original;
        $clone->setId($cloneId);
        $clone->setUserId($userId);

        $this->entityManager->persist($clone);
        $this->entityManager->flush();
    }
}
