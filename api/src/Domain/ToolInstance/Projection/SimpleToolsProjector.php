<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Projection;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Domain\ToolInstance\Event\ToolInstanceDataHasBeenUpdated;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCloned;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCreated;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenDeleted;
use App\Domain\ToolInstance\Event\ToolInstanceMetadataHasBeenUpdated;
use App\Model\Projector;
use App\Model\SimpleToolInstance;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;

final class SimpleToolsProjector extends Projector
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
     * @param ToolInstanceHasBeenCreated $event
     * @throws \Exception
     */
    protected function onToolInstanceHasBeenCreated(ToolInstanceHasBeenCreated $event): void
    {
        $aggregateId = $event->aggregateId();
        $tool = $event->tool();
        $metadata = $event->metadata();
        $data = $event->data();
        $userId = $event->userId();

        $simpleTool = SimpleToolInstance::createWith($aggregateId, $tool);
        $simpleTool->setMetadata($metadata);
        $simpleTool->setData($data);
        $simpleTool->setUserId($userId);

        $this->entityManager->persist($simpleTool);
        $this->entityManager->flush();
    }

    /**
     * @param ToolInstanceHasBeenCloned $event
     * @throws \Exception
     */
    protected function onToolInstanceHasBeenCloned(ToolInstanceHasBeenCloned $event): void
    {

        $simpleTool = $this->simpleToolRepository->findOneBy(['id' => $event->baseId()]);

        if (!($simpleTool instanceof SimpleToolInstance)) {
            return;
        }

        $simpleTool = clone $simpleTool;
        $simpleTool->setId($event->aggregateId());
        $simpleTool->setUserId($event->userId());
        $this->entityManager->persist($simpleTool);
        $this->entityManager->flush();
    }

    /**
     * @param ToolInstanceMetadataHasBeenUpdated $event
     * @throws \Exception
     */
    protected function onToolInstanceMetadataHasBeenUpdated(ToolInstanceMetadataHasBeenUpdated $event): void
    {
        $simpleTool = $this->simpleToolRepository->findOneBy(['id' => $event->aggregateId()]);

        if (!($simpleTool instanceof SimpleToolInstance)) {
            return;
        }

        $simpleTool->setMetadata($event->metadata());
        $this->entityManager->persist($simpleTool);
        $this->entityManager->flush();
    }

    /**
     * @param ToolInstanceDataHasBeenUpdated $event
     * @throws \Exception
     */
    protected function onToolInstanceDataHasBeenUpdated(ToolInstanceDataHasBeenUpdated $event): void
    {
        $simpleTool = $this->simpleToolRepository->findOneBy(['id' => $event->aggregateId()]);

        if (!($simpleTool instanceof SimpleToolInstance)) {
            return;
        }

        $data = $event->data();

        switch ($event->mergeStrategy()) {
            case($event::MERGE_STRATEGY_REPLACE):
                $data = $event->data();
                break;
            case($event::MERGE_STRATEGY_MERGE):
                $data = $this->array_merge_recursive_distinct($simpleTool->getData(), $data);
                break;
            case($event::MERGE_STRATEGY_DELETE):
                $data = $this->array_remove_element($simpleTool->getData(), $data);
                break;
            default:
                $data = $event->data();
        }

        $simpleTool->setData($data);
        $this->entityManager->persist($simpleTool);
        $this->entityManager->flush();
    }

    /**
     * @param ToolInstanceHasBeenDeleted $event
     * @throws \Exception
     */
    protected function onToolInstanceHasBeenDeleted(ToolInstanceHasBeenDeleted $event): void
    {
        $simpleTool = $this->simpleToolRepository->findOneBy(['id' => $event->aggregateId()]);
        $this->entityManager->remove($simpleTool);
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