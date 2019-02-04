<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;
use App\Model\Common\Aggregate;
use App\Model\User\Aggregate\UserAggregate;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;

final class AggregateRepository
{
    private $aggregates = [
        UserAggregate::class
    ];

    private $aggregateMap = [];
    private $eventMap = [];

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventRepository $eventRepository */
    private $eventRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->eventRepository = $entityManager->getRepository(Event::class);

        /** @var Aggregate $aggregate */
        foreach ($this->aggregates as $aggregate) {
            $this->aggregateMap[$aggregate::NAME] = $aggregate;
            array_merge($this->eventMap, $aggregate::eventMap());
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
            if (!array_key_exists($event->getEventName(), $this->eventMap)) {
                throw new \RuntimeException(sprintf('Missing eventType in eventMap class %s', \get_class($this)));
            }

            $classname = $this->eventMap[$event->getEventName()];
            $event = $classname::fromBaseClass($event);
        }

        return $events;
    }

    /**
     * @param string $aggregateId
     * @param bool $applyEvents
     * @return Aggregate
     * @throws \Exception
     */
    public function findAggregateById(string $aggregateId, bool $applyEvents = true): Aggregate
    {
        $event = $this->eventRepository->findOneBy(
            ['aggregateId' => $aggregateId],
            ['id' => 'ASC']
        );

        if (!$event instanceof Event) {
            throw new \Exception('Unknown AggregateId');
        }

        if (!array_key_exists($event->aggregateName(), $this->aggregateMap)) {
            throw new \RuntimeException(sprintf('Missing aggregateType in aggregateMap class %s', \get_class($this)));
        }

        $classname = $this->aggregateMap[$event->aggregateName()];

        /** @var Aggregate $aggregate */
        $aggregate = $classname::withId($aggregateId);

        if ($applyEvents) {
            $events = $this->findEventsByAggregateId($aggregateId);
            foreach ($events as $event) {
                $aggregate->apply($event);
            }
        }

        return $aggregate;
    }

    public function findEventsByAggregateId(string $aggregateId): array
    {
        $events = $this->eventRepository->findBy(
            ['aggregateId' => $aggregateId],
            ['version' => 'ASC']
        );

        /**
         * @var int $key
         * @var  Event $event
         */
        foreach ($events as $key => &$event) {
            if (!array_key_exists($event->getEventName(), $this->eventMap)) {
                throw new \RuntimeException(sprintf('Missing eventType in eventMap class %s', \get_class($this)));
            }

            $classname = $this->eventMap[$event->getEventName()];
            $event = $classname::fromBaseClass($event);
        }

        return $events;
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
