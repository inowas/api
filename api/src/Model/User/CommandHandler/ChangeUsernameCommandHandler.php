<?php

declare(strict_types=1);

namespace App\Model\User\CommandHandler;

use App\Entity\User;
use App\Model\User\Command\ChangeUsernameCommand;
use App\Model\User\Event\UsernameHasBeenChanged;
use App\Model\User\Projector\UserProjector;
use App\Repository\AggregateRepository;
use App\Service\UserManager;

class ChangeUsernameCommandHandler
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
     * @param ChangeUsernameCommand $command
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function __invoke(ChangeUsernameCommand $command)
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

        if ($user->getUsername() === $command->username()) {
            // Nothing to change
            return;
        }

        $aggregateId = $userId;

        // This is a simple check if the aggregate exists, so we do not need to apply any event
        $this->aggregateRepository->findAggregateById($aggregateId, false);

        $event = UsernameHasBeenChanged::fromParams($aggregateId, $command->username());
        $this->aggregateRepository->storeEvent($event);
        $this->userProjector->apply($event);
    }
}
