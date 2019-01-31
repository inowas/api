<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Model\User\Event\UserHasBeenCreated;
use Doctrine\ORM\EntityManagerInterface;

final class UserProjection
{

    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param UserHasBeenCreated $event
     * @throws \Exception
     */
    public function onUserHasBeenCreated(UserHasBeenCreated $event): void
    {
        $user = new User($event->username(), $event->password(), $event->roles(), $event->isEnabled());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
