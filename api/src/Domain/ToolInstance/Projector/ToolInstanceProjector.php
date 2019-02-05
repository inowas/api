<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Projector;

use App\Domain\Common\Projector;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCreated;
use App\Entity\ToolInstance;
use App\Entity\User;
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