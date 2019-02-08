<?php

declare(strict_types=1);

namespace App\Domain\User\CommandHandler;

use App\Domain\User\Aggregate\UserAggregate;
use App\Repository\AggregateRepository;
use App\Domain\User\Command\ChangeUserProfileCommand;
use App\Domain\User\Event\UserProfileHasBeenChanged;
use App\Domain\User\Projection\UserProjector;
use App\Model\User;
use App\Service\UserManager;

class ChangeUserProfileCommandHandler
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
     * @param ChangeUserProfileCommand $command
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function __invoke(ChangeUserProfileCommand $command)
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

        if ($user->getProfile() == $command->profile()) {
            return; // Nothing to do here
        }

        $aggregateId = $userId;
        $event = UserProfileHasBeenChanged::fromParams($aggregateId, $command->profile());
        $aggregate = $this->aggregateRepository->findAggregateById(UserAggregate::class, $aggregateId);
        $aggregate->apply($event);

        $this->aggregateRepository->storeEvent($event);
        $this->userProjector->apply($event);
    }
}
