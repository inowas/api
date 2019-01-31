<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;
use App\Model\User\Aggregate\UserAggregate;
use App\Model\User\Event\UserHasBeenCreated;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;

final class AggregateRepository
{
    private $aggregateNamesMap = [
        UserAggregate::NAME => UserAggregate::class
    ];

    private $eventNamesMap = [
        UserHasBeenCreated::NAME => UserHasBeenCreated::class
    ];

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventRepository $eventRepository */
    private $eventRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->eventRepository = $entityManager->getRepository(Event::class);
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
            if (!array_key_exists($event->eventName(), $this->eventNamesMap)) {
                throw new \RuntimeException(sprintf('Missing eventType in eventMap class %s', \get_class($this)));
            }

            $classname = $this->eventNamesMap[$event->eventName()];
            $event = $classname::fromBaseClass($event);
        }

        return $events;
    }

    /**
     * @param string $aggregateId
     * @return array
     * @throws \Exception
     */
    public function findAggregateById(string $aggregateId): array
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
