<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\McdaDeleteCriterionCommand;
use App\Model\Mcda\Mcda;
use Doctrine\ORM\EntityManagerInterface;

final class McdaDeleteCriterionCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param McdaDeleteCriterionCommand $command
     * @throws \Exception
     */
    public function __invoke(McdaDeleteCriterionCommand $command)
    {
        $id = $command->id();
        $userId = $command->metadata()['user_id'];

        $mcda = $this->entityManager->getRepository(Mcda::class)->findOneBy(['id' => $id]);

        if (!$mcda instanceof Mcda || $mcda->tool() !== 'T05') {
            throw new \Exception('Mcda-Project not found');
        }

        if ($mcda->userId() !== $userId) {
            throw new \Exception('The Model cannot be updated due to permission problems.');
        }

        $mcda->removeCriterion($command->criterionId());
        $this->entityManager->persist($mcda);
        $this->entityManager->flush();
    }
}
