<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\UpdateModflowModelDiscretizationCommand;
use App\Model\Modflow\ModflowModel;
use App\Model\ProjectorCollection;
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

        $modflowModel = $this->entityManager->getRepository(ModflowModel::class)->findOneBy(['id' => $modelId]);

        if (!$modflowModel instanceof ModflowModel) {
            throw new \Exception('ModflowModel not found');
        }

        if ($modflowModel->userId() !== $userId) {
            throw new \Exception('The Model cannot be updated due to permission problems.');
        }

        $modflowModel->setDiscretization($command->discretization());
        $this->entityManager->persist($modflowModel);
        $this->entityManager->flush();
    }
}
