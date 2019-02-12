<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Projection;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Domain\ToolInstance\Event\ModflowModelHasBeenCreated;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCloned;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCreated;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenDeleted;
use App\Domain\ToolInstance\Event\ToolInstanceMetadataHasBeenUpdated;
use App\Model\DashboardItem;
use App\Model\Projector;
use App\Model\User;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;

final class DashboardProjector extends Projector
{

    private $entityManager;
    private $userManager;
    private $dashboardItemRepository;

    public function __construct(EntityManagerInterface $entityManager, UserManager $userManager)
    {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
        $this->dashboardItemRepository = $entityManager->getRepository(DashboardItem::class);
    }

    public function aggregateName(): string
    {
        return ToolInstanceAggregate::NAME;
    }

    /**
     * @param ModflowModelHasBeenCreated $event
     * @throws \Exception
     */
    protected function onModflowModelHasBeenCreated(ModflowModelHasBeenCreated $event): void
    {
        $user = $this->userManager->findUserById($event->userId());
        $username = ($user instanceof User) ? $user->getUsername() : '';
        $metadata = $event->metadata();
        $dashboardItem = DashboardItem::createFromId($event->aggregateId(), $event->tool());
        $dashboardItem->setName($metadata->name());
        $dashboardItem->setDescription($metadata->description());
        $dashboardItem->setIsPublic($metadata->isPublic());
        $dashboardItem->setUserId($event->userId());
        $dashboardItem->setUsername($username);
        $dashboardItem->setCreatedAt($event->createdAt());
        $this->entityManager->persist($dashboardItem);
        $this->entityManager->flush();
    }

    /**
     * @param ToolInstanceHasBeenCreated $event
     * @throws \Exception
     */
    protected function onToolInstanceHasBeenCreated(ToolInstanceHasBeenCreated $event): void
    {
        $user = $this->userManager->findUserById($event->userId());
        $username = ($user instanceof User) ? $user->getUsername() : '';
        $metadata = $event->metadata();
        $dashboardItem = DashboardItem::createFromId($event->aggregateId(), $event->tool());
        $dashboardItem->setName($metadata->name());
        $dashboardItem->setDescription($metadata->description());
        $dashboardItem->setIsPublic($metadata->isPublic());
        $dashboardItem->setUserId($event->userId());
        $dashboardItem->setUsername($username);
        $dashboardItem->setCreatedAt($event->createdAt());
        $this->entityManager->persist($dashboardItem);
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

        $dashboardItem = $this->dashboardItemRepository->findOneBy(['id' => $event->baseId()]);

        if (!($dashboardItem instanceof DashboardItem)) {
            throw new \Exception('DashboardItem not found');
        }

        $dashboardItem = clone $dashboardItem;
        $dashboardItem->setId($event->aggregateId());
        $dashboardItem->setUserId($event->userId());
        $dashboardItem->setUsername($username);
        $dashboardItem->setCreatedAt($event->createdAt());
        $this->entityManager->persist($dashboardItem);
        $this->entityManager->flush();
    }

    /**
     * @param ToolInstanceMetadataHasBeenUpdated $event
     * @throws \Exception
     */
    protected function onToolInstanceMetadataHasBeenUpdated(ToolInstanceMetadataHasBeenUpdated $event): void
    {
        $user = $this->userManager->findUserById($event->userId());
        $username = ($user instanceof User) ? $user->getUsername() : '';
        $dashboardItem = $this->dashboardItemRepository->findOneBy(['id' => $event->aggregateId()]);
        if (!($dashboardItem instanceof DashboardItem)) {
            throw new \Exception('DashboardItem not found');
        }

        $metadata = $event->metadata();
        $dashboardItem->setName($metadata->name());
        $dashboardItem->setDescription($metadata->description());
        $dashboardItem->setIsPublic($metadata->isPublic());
        $dashboardItem->setUsername($username);

        $this->entityManager->persist($dashboardItem);
        $this->entityManager->flush();
    }

    /**
     * @param ToolInstanceHasBeenDeleted $event
     * @throws \Exception
     */
    protected function onToolInstanceHasBeenDeleted(ToolInstanceHasBeenDeleted $event): void
    {
        $dashboardItem = $this->dashboardItemRepository->findOneBy(['id' => $event->aggregateId()]);
        $this->entityManager->remove($dashboardItem);
        $this->entityManager->flush();
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function truncateTable(): void
    {
        $cmd = $this->entityManager->getClassMetadata(DashboardItem::class);
        $connection = $this->entityManager->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
        $connection->executeUpdate($q);
    }
}
