<?php

declare(strict_types=1);

namespace App\Model\User\CommandHandler;

use App\Entity\User;
use App\Model\User\Command\ArchiveUserCommand;
use App\Model\User\Event\UserHasBeenArchived;
use App\Service\AggregateRepository;
use App\Service\UserManager;
use App\Service\UserProjection;

class ArchiveUserCommandHandler
{
    /** @var AggregateRepository */
    private $aggregateRepository;

    /** @var UserProjection */
    private $userProjection;

    /** @var UserManager */
    private $userManager;


    public function __construct(AggregateRepository $aggregateRepository, UserManager $userManager, UserProjection $userProjection)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->userManager = $userManager;
        $this->userProjection = $userProjection;
    }

    /**
     * @param ArchiveUserCommand $command
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function __invoke(ArchiveUserCommand $command)
    {
        $isAdmin = $command->metadata()['is_admin'];
        $userId = $command->metadata()['user_id'];

        if (($isAdmin && $command->userId())) {
            $userId = $command->userId();
        }

        // Is it different from the old one?
        $user = $this->userManager->findUserById($userId);

        if (!$user instanceof User) {
            throw new \Exception('User not found');
        }

        $aggregateId = $userId;

        // This is a simple check if the aggregate exists
        $this->aggregateRepository->findAggregateById($aggregateId);

        $event = UserHasBeenArchived::fromParams($aggregateId);
        $this->aggregateRepository->storeEvent($event);
        $this->userProjection->apply($event);
    }
}
