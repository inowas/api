<?php

declare(strict_types=1);

namespace App\Domain\User\CommandHandler;

use App\Domain\User\Aggregate\UserAggregate;
use App\Domain\User\Command\CreateUserCommand;
use App\Domain\User\Event\UserHasBeenCreated;
use App\Domain\User\Projection\UserProjector;
use App\Model\ProjectorCollection;
use App\Repository\AggregateRepository;
use App\Service\UserManager;
use Ramsey\Uuid\Uuid;

class CreateUserCommandHandler
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
     * @param CreateUserCommand $command
     * @throws \Exception
     */
    public function __invoke(CreateUserCommand $command)
    {
        $username = $command->username();
        $password = $command->password();
        $roles = $command->roles();
        $isEnabled = $command->isEnabled();

        if (!$this->userManager->usernameIsValidAndAvailable($username)) {
            throw new \Exception('Username already in use');
        };

        $encryptedPassword = $this->userManager->encryptPassword($password);

        $aggregateId = Uuid::uuid4()->toString();
        $event = UserHasBeenCreated::fromParams(
            $aggregateId, $username, $encryptedPassword, $roles, $isEnabled
        );

        $aggregate = UserAggregate::withId($aggregateId);
        $aggregate->apply($event);

        $this->aggregateRepository->storeEvent($event);
        $this->projectors->getProjector(UserProjector::class)->apply($event);
    }
}
