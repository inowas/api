<?php

declare(strict_types=1);

namespace App\Model\User\CommandHandler;

use App\Entity\User;
use App\Model\User\Command\DeleteUserCommand;
use App\Model\User\Event\UserHasBeenDeleted;
use App\Model\User\Projector\UserProjector;
use App\Repository\AggregateRepository;
use App\Service\UserManager;

class DeleteUserCommandHandler
{
    /** @var AggregateRepository */
    private $aggregateRepository;

    /** @var UserProjector */
    private $userProjector;

    /** @var UserManager */
    private $userManager;


    public function __construct(AggregateRepository $aggregateRepository, UserManager $userManager, UserProjector $userProjector)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->userManager = $userManager;
        $this->userProjector = $userProjector;
    }

    /**
     * @param DeleteUserCommand $command
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function __invoke(DeleteUserCommand $command)
    {
        $isAdmin = $command->metadata()['is_admin'];

        if (!$isAdmin) {
            throw new \Exception('Bad credentials. Please use your admin-account.');
        }

        $executorId = $command->metadata()['user_id'];
        $userToDeleteId = $command->userId();

        if ($executorId === $userToDeleteId) {
            throw new \Exception('You cannot delete your own identity. Please ask another admin.');
        }


        $user = $this->userManager->findUserById($userToDeleteId);
        if (!$user instanceof User) {
            throw new \Exception('User not found, already deleted?');
        }

        $aggregateId = $userToDeleteId;

        // This is a simple check if the aggregate exists, so we do not need to apply any event
        $this->aggregateRepository->findAggregateById($aggregateId, false);

        $event = UserHasBeenDeleted::fromParams($aggregateId);
        $this->aggregateRepository->storeEvent($event);
        $this->userProjector->apply($event);
    }
}
