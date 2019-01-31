<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;
use App\Model\User\Event\UserHasBeenCreated;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;

final class AggregateRepository
{
    private $eventTypeMap = [
        UserHasBeenCreated::TYPE => UserHasBeenCreated::class
    ];

    private $entityManager;

    /** @var EventRepository $eventRepository */
    private $eventRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->eventRepository = $entityManager->getRepository(Event::class);
    }

    /**
     * @param Event $event
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function storeEvent(Event $event): bool
    {
        $version = $this->eventRepository->getVersion($event->aggregateId());
        $event->withVersion($version);

        if (is_subclass_of($event, Event::class)) {
            $event = $event->toBaseClass();
        }

        $this->entityManager->persist($event);
        $this->entityManager->flush();
        return true;
    }
}
