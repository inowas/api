<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;
use App\Model\User\Aggregate\UserAggregate;
use App\Model\User\Event\UserHasBeenArchived;
use App\Model\User\Event\UserHasBeenCreated;
use App\Model\User\Event\UserHasBeenDeleted;
use App\Model\User\Event\UserHasBeenReactivated;
use App\Model\User\Event\UsernameHasBeenChanged;
use App\Model\User\Event\UserPasswordHasBeenChanged;
use App\Model\User\Event\UserProfileHasBeenChanged;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;

final class AggregateRepository
{
    private $aggregates = [
        UserAggregate::class
    ];

    private $events =  [
        UserHasBeenArchived::class,
        UserHasBeenCreated::class,
        UserHasBeenDeleted::class,
        UserHasBeenReactivated::class,
        UsernameHasBeenChanged::class,
        UserPasswordHasBeenChanged::class,
        UserProfileHasBeenChanged::class
    ];

    private $aggregateNamesMap = [];
    private $eventNamesMap = [];

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventRepository $eventRepository */
    private $eventRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->eventRepository = $entityManager->getRepository(Event::class);

        foreach ($this->events as $classname) {
            $this->eventNamesMap[$classname::getEventNameFromClassname()] = $classname;
        }

        foreach ($this->aggregates as $classname) {
            $this->aggregateNamesMap[$classname::NAME] = $classname;
        }
    }

    public function getEventsByAggregateName(string $aggregateName): array
    {

        $events = $this->eventRepository->findBy(
            ['aggregateName' => $aggregateName],
            ['id' => 'ASC']
        );

        /**
         * @var int $key
         * @var  Event $event
         */
        foreach ($events as $key => &$event) {
            var_dump($this->eventNamesMap, $event->getEventName());

            if (!array_key_exists($event->getEventName(), $this->eventNamesMap)) {
                throw new \RuntimeException(sprintf('Missing eventType in eventMap class %s', \get_class($this)));
            }

            $classname = $this->eventNamesMap[$event->getEventName()];
            $event = $classname::fromBaseClass($event);
        }

        return $events;
    }

    /**
     * @param string $aggregateId
     * @return array
     * @throws \Exception
     */
    public function findAggregateById(string $aggregateId)
    {
        $event = $this->eventRepository->findOneBy(
            ['aggregateId' => $aggregateId],
            ['id' => 'ASC']
        );

        if (!$event instanceof Event) {
            throw new \Exception('Unknown AggregateId');
        }

        if (!array_key_exists($event->aggregateName(), $this->aggregateNamesMap)) {
            throw new \RuntimeException(sprintf('Missing aggregateType in aggregateMap class %s', \get_class($this)));
        }

        $classname = $this->aggregateNamesMap[$event->aggregateName()];
        $aggregate = $classname::withId($aggregateId);
        return $aggregate;
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
