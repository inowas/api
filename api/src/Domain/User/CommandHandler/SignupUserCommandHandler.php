<?php

declare(strict_types=1);

namespace App\Domain\User\CommandHandler;

use App\Domain\User\Aggregate\UserAggregate;
use App\Domain\User\Command\SignupUserCommand;
use App\Domain\User\Event\UserHasBeenCreated;
use App\Domain\User\Event\UserProfileHasBeenChanged;
use App\Domain\User\Projection\UserProjector;
use App\Model\ProjectorCollection;
use App\Repository\AggregateRepository;
use App\Service\UserManager;
use Ramsey\Uuid\Uuid;

class SignupUserCommandHandler
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
     * @param SignupUserCommand $command
     * @throws \Exception
     */
    public function __invoke(SignupUserCommand $command)
    {
        $username = $command->email();
        $password = $command->password();
        $roles = ['ROLE_USER'];
        $isEnabled = true;

        if (!$this->userManager->usernameIsValidAndAvailable($username)) {
            throw new \Exception('Username already in use');
        };

        $encryptedPassword = $this->userManager->encryptPassword($password);

        $aggregateId = Uuid::uuid4()->toString();
        $createUserEvent = UserHasBeenCreated::fromParams(
            $aggregateId, $username, $encryptedPassword, $roles, $isEnabled
        );

        $aggregate = UserAggregate::withId($aggregateId);
        $aggregate->apply($createUserEvent);

        $this->aggregateRepository->storeEvent($createUserEvent);
        $this->projectors->getProjector(UserProjector::class)->apply($createUserEvent);

        $updateProfileEvent = UserProfileHasBeenChanged::fromParams($aggregateId, [
            'name' => $command->name(),
            'email' => $command->email()
        ]);

        $aggregate->apply($updateProfileEvent);
        $this->aggregateRepository->storeEvent($updateProfileEvent);
        $this->projectors->getProjector(UserProjector::class)->apply($updateProfileEvent);
    }
}
