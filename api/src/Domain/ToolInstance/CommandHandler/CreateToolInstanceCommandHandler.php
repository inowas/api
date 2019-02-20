<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\CommandHandler;

use App\Domain\ToolInstance\Command\CreateToolInstanceCommand;

use App\Model\Mcda\Mcda;
use App\Model\Modflow\Discretization;
use App\Model\Modflow\ModflowModel;
use App\Model\SimpleTool\SimpleTool;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;

class CreateToolInstanceCommandHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param CreateToolInstanceCommand $command
     * @throws \Exception
     */
    public function __invoke(CreateToolInstanceCommand $command)
    {
        $id = $command->id();
        $userId = $command->metadata()['user_id'];
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $userId]);

        if (!$user instanceof User) {
            throw new \Exception(sprintf('User with id %s not found.', $userId));
        }
        $tool = $command->tool();
        $metadata = $command->toolMetadata();
        $data = $command->data();

        switch ($tool) {
            case 'T03':
                $instance = ModflowModel::createWithParams($id, $user, $tool, $metadata);
                $instance->setDiscretization(Discretization::fromArray($command->data()));
                break;
            case 'T05':
                $instance = Mcda::createWithParams($id, $user, $tool, $metadata);
                $instance->setData($command->data());
                break;
            default:
                $instance = SimpleTool::createWithParams($id, $user, $tool, $metadata);
                $instance->setData($command->data());
        }

        $instance->setData($data);
        $this->entityManager->persist($instance);
        $this->entityManager->flush();
    }
}
