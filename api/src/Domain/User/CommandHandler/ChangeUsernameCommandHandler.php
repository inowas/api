<?php

declare(strict_types=1);

namespace App\Domain\User\CommandHandler;

use App\Domain\User\Aggregate\UserAggregate;
use App\Model\ProjectorCollection;
use App\Repository\AggregateRepository;
use App\Domain\User\Command\ChangeUsernameCommand;
use App\Domain\User\Event\UsernameHasBeenChanged;
use App\Domain\User\Projection\UserProjector;
use App\Model\User;
use App\Service\UserManager;

class ChangeUsernameCommandHandler
{
    /** @var AggregateRepository */
    private $aggregateRepository;

    /** @var ProjectorCollection */
    private $projectors;

    /** @var UserManager */
    private $userManager;


    public function __construct(AggregateRepository $aggregateRepository, UserManager $userManager, ProjectorCollection $projectors)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->projectors = $projectors;
        $this->userManager = $userManager;
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
        $event = UsernameHasBeenChanged::fromParams($aggregateId, $command->username());
        $aggregate = $this->aggregateRepository->findAggregateById(UserAggregate::class, $aggregateId);
        $aggregate->apply($event);

        $this->aggregateRepository->storeEvent($event);
        $this->projectors->getProjector(UserProjector::class)->apply($event);
    }
}
