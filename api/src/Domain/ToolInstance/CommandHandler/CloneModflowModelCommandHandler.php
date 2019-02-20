<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\CloneModflowModelCommand;
use App\Model\Modflow\ModflowModel;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;

class CloneModflowModelCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param CloneModflowModelCommand $command
     * @throws \Exception
     */
    public function __invoke(CloneModflowModelCommand $command)
    {
        $userId = $command->metadata()['user_id'];
        $originId = $command->id();
        $cloneId = $command->newId();
        $cloneAsTool = $command->isTool();

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $userId]);
        if (!$user instanceof User) {
            throw new \Exception(sprintf('User with id %s not found.', $userId));
        }

        $original = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $originId]);

        if (!$original instanceof ModflowModel) {
            throw new \Exception('ModflowModel not found');
        }

        # The user needs to be the owner of the model or the model has to be public
        $canBeCloned = ($userId === $original->userId() || true === $original->isPublic());
        if (!$canBeCloned) {
            throw new \Exception('The ModflowModel cannot be cloned due to permission problems.');
        }

        /** @var ModflowModel $clone */
        $clone = clone $original;
        $clone->setId($cloneId);
        $clone->setUser($user);
        $clone->setIsScenario(!$cloneAsTool);

        $this->entityManager->persist($clone);
        $this->entityManager->flush();
    }
}
