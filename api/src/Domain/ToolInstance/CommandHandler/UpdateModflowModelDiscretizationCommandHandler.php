<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\UpdateModflowModelDiscretizationCommand;
use App\Model\Modflow\ModflowModel;
use App\Model\ProjectorCollection;
use App\Model\ToolInstance;
use App\Repository\AggregateRepository;
use Doctrine\ORM\EntityManagerInterface;

class UpdateModflowModelDiscretizationCommandHandler
{
    /** @var AggregateRepository */
    private $aggregateRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ProjectorCollection */
    private $projectors;

    public function __construct(AggregateRepository $aggregateRepository, EntityManagerInterface $entityManager, ProjectorCollection $projectors)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->entityManager = $entityManager;
        $this->projectors = $projectors;
    }

    /**
     * @param UpdateModflowModelDiscretizationCommand $command
     * @throws \Exception
     */
    public function __invoke(UpdateModflowModelDiscretizationCommand $command)
    {
        $modelId = $command->id();
        $userId = $command->metadata()['user_id'];

        $toolInstance = $this->entityManager->getRepository(ToolInstance::class)->findOneBy(['id' => $modelId]);

        if (!$toolInstance instanceof ToolInstance) {
            throw new \Exception('ToolInstance not found');
        }

        if ($toolInstance->userId() !== $userId) {
            throw new \Exception('The Model cannot be updated due to permission problems.');
        }

        $modflowModel = ModflowModel::fromArray($toolInstance->getData());
        $modflowModel->setDiscretization($command->discretization());
        $toolInstance->setData($modflowModel->toArray());

        $this->entityManager->persist($toolInstance);
        $this->entityManager->flush();
    }
}
