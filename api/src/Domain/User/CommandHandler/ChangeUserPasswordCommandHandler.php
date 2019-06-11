<?php

declare(strict_types=1);

namespace App\Domain\User\CommandHandler;

use App\Domain\User\Aggregate\UserAggregate;
use App\Domain\User\Exception\PasswordInvalidException;
use App\Model\ProjectorCollection;
use App\Repository\AggregateRepository;
use App\Domain\User\Command\ChangeUserPasswordCommand;
use App\Domain\User\Event\UserPasswordHasBeenChanged;
use App\Domain\User\Projection\UserProjector;
use App\Model\User;
use App\Service\UserManager;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChangeUserPasswordCommandHandler
{
    /** @var AggregateRepository */
    private $aggregateRepository;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var ProjectorCollection */
    private $projectors;

    /** @var UserManager */
    private $userManager;


    public function __construct(AggregateRepository $aggregateRepository, UserManager $userManager, ProjectorCollection $projectors, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->projectors = $projectors;
        $this->userManager = $userManager;
    }

    /**
     * @param ChangeUserPasswordCommand $command
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function __invoke(ChangeUserPasswordCommand $command)
    {
        $isAdmin = $command->metadata()['is_admin'];
        $userId = $command->metadata()['user_id'];

        if ($isAdmin && $command->userId()) {
            $userId = $command->userId();
        }

        // Is it different from the old one?
        $user = $this->userManager->findUserById($userId);

        if (!$user instanceof User) {
            throw new Exception('User not found');
        }

        if (!$isAdmin && !$this->passwordEncoder->isPasswordValid($user, $command->password())) {
            throw new PasswordInvalidException('The current password is wrong.', 400);
        }

        $newPassword = $this->userManager->encryptPassword($command->newPassword());

        $aggregateId = $userId;
        $event = UserPasswordHasBeenChanged::fromParams($aggregateId, $newPassword);
        $aggregate = $this->aggregateRepository->findAggregateById(UserAggregate::class, $aggregateId);
        $aggregate->apply($event);

        $this->aggregateRepository->storeEvent($event);
        $this->projectors->getProjector(UserProjector::class)->apply($event);
    }
}
