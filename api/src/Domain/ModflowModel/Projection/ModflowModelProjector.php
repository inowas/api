<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\Projection;

use App\Domain\ModflowModel\Aggregate\ModflowModelAggregate;
use App\Domain\ModflowModel\Event\ModflowModelHasBeenCreated;
use App\Domain\ModflowModel\Event\ModflowModelHasBeenDeleted;
use App\Domain\ModflowModel\Event\ModflowModelHasBeenUpdated;
use App\Model\ModflowModel;
use App\Model\Projector;
use Doctrine\ORM\EntityManagerInterface;

class ModflowModelProjector extends Projector
{
    private $entityManager;
    private $modflowModelRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->modflowModelRepository = $entityManager->getRepository(ModflowModel::class);
    }

    public function aggregateName(): string
    {
        return ModflowModelAggregate::NAME;
    }

    /**
     * @param ModflowModelHasBeenCreated $event
     * @throws \Exception
     */
    protected function onModflowModelHasBeenCreated(ModflowModelHasBeenCreated $event): void
    {
        $modflowModel = ModflowModel::createFromId($event->aggregateId());
        $modflowModel->setName($event->name());
        $modflowModel->setDescription($event->description());
        $modflowModel->setUserId($event->userId());
        $modflowModel->setIsPublic($event->isPublic());
        $modflowModel->setCreatedAt($event->createdAt());
        $modflowModel->setDiscretization($event->discretization());
        $this->entityManager->persist($modflowModel);
        $this->entityManager->flush();
    }

    /**
     * @param ModflowModelHasBeenUpdated $event
     * @throws \Exception
     */
    protected function onModflowModelHasBeenUpdated(ModflowModelHasBeenUpdated $event): void
    {

        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->modflowModelRepository->findOneBy(['id' => $event->aggregateId()]);
        null !== $event->name() && $modflowModel->setName($event->name());
        null !== $event->description() && $modflowModel->setDescription($event->description());
        null !== $event->description() && $modflowModel->setDescription($event->description());
        null !== $event->userId() && $modflowModel->setUserId($event->userId());
        null !== $event->isPublic() && $modflowModel->setIsPublic($event->isPublic());

        $discretization = $modflowModel->getDiscretization();
        foreach ($event->discretization() as $key => $value) {
            if (null !== $value) {
                $discretization[$key] = $value;
            }
        }
        $modflowModel->setDiscretization($discretization);
        $this->entityManager->persist($modflowModel);
        $this->entityManager->flush();
    }

    /**
     * @param ModflowModelHasBeenDeleted $event
     * @throws \Exception
     */
    protected function onModflowModelHasBeenDeleted(ModflowModelHasBeenDeleted $event): void
    {
        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->modflowModelRepository->findOneBy(['id' => $event->aggregateId()]);
        $this->entityManager->remove($modflowModel);
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
