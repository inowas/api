<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Projection;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Domain\ToolInstance\Event\ModflowModelHasBeenCreated;
use App\Domain\ToolInstance\Event\ModflowModelMetadataHasBeenUpdated;
use App\Domain\ToolInstance\Event\ToolInstanceDataHasBeenUpdated;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCloned;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCreated;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenDeleted;
use App\Domain\ToolInstance\Event\ToolInstanceMetadataHasBeenUpdated;
use App\Model\Modflow\ModflowModel;
use App\Model\Projector;
use App\Model\SimpleToolInstance;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;

final class ModflowModelProjector extends Projector
{

    private $entityManager;
    private $userManager;
    private $simpleToolRepository;

    public function __construct(EntityManagerInterface $entityManager, UserManager $userManager)
    {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
        $this->simpleToolRepository = $entityManager->getRepository(SimpleToolInstance::class);
    }

    public function aggregateName(): string
    {
        return ToolInstanceAggregate::NAME;
    }

    /**
     * @param ModflowModelHasBeenCreated $event
     */
    protected function onModflowModelHasBeenCreated(ModflowModelHasBeenCreated $event): void
    {
        $aggregateId = $event->aggregateId();
        $userId = $event->userId();
        $modflowModel = $event->modflowModel();
        $modflowModel->setId($aggregateId);
        $modflowModel->setUserId($userId);

        $this->entityManager->persist($modflowModel);
        $this->entityManager->flush();
    }

    /**
     * @param ModflowModelMetadataHasBeenUpdated $event
     */
    protected function onModflowModelMetadataHasBeenUpdated(ModflowModelMetadataHasBeenUpdated $event): void
    {
        $aggregateId = $event->aggregateId();
        $userId = $event->userId();

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $event->aggregateId()]);

        $metadata = $modflowModel->getMetadata();

        $modflowModel = $event->modflowModel();
        $modflowModel->setId($aggregateId);
        $modflowModel->setUserId($userId);

        $this->entityManager->persist($modflowModel);
        $this->entityManager->flush();
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function truncateTable(): void
    {
        $cmd = $this->entityManager->getClassMetadata(SimpleToolInstance::class);
        $connection = $this->entityManager->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
        $connection->executeUpdate($q);
    }
}
