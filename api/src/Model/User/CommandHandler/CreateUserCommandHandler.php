<?php

declare(strict_types=1);

namespace App\Model\User\CommandHandler;

use App\Model\User\Command\CreateUserCommand;
use App\Model\User\Event\UserHasBeenCreated;
use App\Service\AggregateRepository;
use App\Service\UserManager;
use App\Service\UserProjection;
use Ramsey\Uuid\Uuid;

class CreateUserCommandHandler
{
    /** @var AggregateRepository */
    private $aggregateRepository;

    /** @var UserManager */
    private $userManager;

    /** @var UserProjection */
    private $userProjection;


    public function __construct(AggregateRepository $aggregateRepository, UserManager $userManager, UserProjection $userProjection)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->userManager = $userManager;
        $this->userProjection = $userProjection;
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

        $this->aggregateRepository->storeEvent($event);
        $this->userProjection->apply($event);
    }
}
