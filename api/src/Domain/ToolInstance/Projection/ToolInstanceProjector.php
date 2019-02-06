<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Projection;

use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCloned;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenDeleted;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenUpdated;
use App\Model\Projector;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCreated;
use App\Model\ToolInstance;
use App\Model\User;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;

final class ToolInstanceProjector extends Projector
{

    private $entityManager;
    private $userManager;
    private $toolInstanceRepository;

    public function __construct(EntityManagerInterface $entityManager, UserManager $userManager)
    {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
        $this->toolInstanceRepository = $entityManager->getRepository(ToolInstance::class);
    }

    public function aggregateName(): string
    {
        return \App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate::NAME;
    }

    /**
     * @param ToolInstanceHasBeenCreated $event
     * @throws \Exception
     */
    protected function onToolInstanceHasBeenCreated(ToolInstanceHasBeenCreated $event): void
    {
        $user = $this->userManager->findUserById($event->userId());
        $username = ($user instanceof User) ? $user->getUsername() : '';

        $toolInstance = ToolInstance::createFromId($event->aggregateId(), $event->tool());
        $toolInstance->setName($event->name());
        $toolInstance->setDescription($event->description());
        $toolInstance->setIsPublic($event->isPublic());
        $toolInstance->setData($event->data());
        $toolInstance->setUserId($event->userId());
        $toolInstance->setUsername($username);
        $toolInstance->setCreatedAt($event->createdAt());
        $this->entityManager->persist($toolInstance);
        $this->entityManager->flush();
    }

    /**
     * @param ToolInstanceHasBeenCloned $event
     * @throws \Exception
     */
    protected function onToolInstanceHasBeenCloned(ToolInstanceHasBeenCloned $event): void
    {
        $user = $this->userManager->findUserById($event->userId());
        $username = ($user instanceof User) ? $user->getUsername() : '';

        $toolInstance = $this->toolInstanceRepository->findOneBy(['id' => $event->baseId()]);

        if (!($toolInstance instanceof ToolInstance)) {
            throw new \Exception('ToolInstance not found');
        }

        $toolInstance = clone $toolInstance;
        $toolInstance->setId($event->aggregateId());
        $toolInstance->setUserId($event->userId());
        $toolInstance->setUsername($username);
        $toolInstance->setCreatedAt($event->createdAt());
        $this->entityManager->persist($toolInstance);
        $this->entityManager->flush();
    }

    /**
     * @param ToolInstanceHasBeenUpdated $event
     * @throws \Exception
     */
    protected function onToolInstanceHasBeenUpdated(ToolInstanceHasBeenUpdated $event): void
    {
        $user = $this->userManager->findUserById($event->userId());
        $username = ($user instanceof User) ? $user->getUsername() : '';

        $toolInstance = $this->toolInstanceRepository->findOneBy(['id' => $event->aggregateId()]);

        if (!($toolInstance instanceof ToolInstance)) {
            throw new \Exception('ToolInstance not found');
        }

        $event->name() && $toolInstance->setName($event->name());
        $event->description() && $toolInstance->setDescription($event->description());
        $event->isPublic() && $toolInstance->setIsPublic($event->isPublic());
        $event->data() && $toolInstance->setData($event->data());
        $toolInstance->setUsername($username);

        $this->entityManager->persist($toolInstance);
        $this->entityManager->flush();
    }

    /**
     * @param ToolInstanceHasBeenDeleted $event
     * @throws \Exception
     */
    protected function onToolInstanceHasBeenDeleted(ToolInstanceHasBeenDeleted $event): void
    {
        $toolInstance = $this->toolInstanceRepository->findOneBy(['id' => $event->aggregateId()]);
        $this->entityManager->remove($toolInstance);
        $this->entityManager->flush();
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function truncateTable(): void
    {
        $cmd = $this->entityManager->getClassMetadata(ToolInstance::class);
        $connection = $this->entityManager->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
        $connection->executeUpdate($q);
    }
}
