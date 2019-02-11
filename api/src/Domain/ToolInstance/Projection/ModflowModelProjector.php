<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Projection;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Domain\ToolInstance\Event\ModflowModelHasBeenCreated;
use App\Model\Modflow\ModflowModel;
use App\Model\Projector;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;

final class ModflowModelProjector extends Projector
{

    private $entityManager;
    private $userManager;
    private $modflowModelRepository;

    public function __construct(EntityManagerInterface $entityManager, UserManager $userManager)
    {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
        $this->modflowModelRepository = $entityManager->getRepository(ModflowModel::class);
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
        $aggregateId = $event->aggregateId();
        $metadata = $event->metadata();

        $discretization = $event->discretization();

        $userId = $event->userId();

        $modflowModel = ModflowModel::createFromId($aggregateId);
        $modflowModel->setMetadata($metadata);
        $modflowModel->setDiscretization($discretization);
        $modflowModel->setUserId($userId);
        $this->entityManager->persist($modflowModel);
        $this->entityManager->flush();
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function truncateTable(): void
    {
        $cmd = $this->entityManager->getClassMetadata(ModflowModel::class);
        $connection = $this->entityManager->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
        $connection->executeUpdate($q);
    }
}
