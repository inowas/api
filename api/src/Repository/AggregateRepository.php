<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\DomainEvent;
use App\Domain\User\Aggregate\UserAggregate;
use App\Model\Aggregate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

final class AggregateRepository
{
    private $aggregates = [
        UserAggregate::class,
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
        $this->eventRepository = $entityManager->getRepository(DomainEvent::class);

        /** @var Aggregate $aggregate */
        foreach ($this->aggregates as $aggregate) {
            $this->aggregateMap[$aggregate::NAME] = $aggregate;
            $this->eventMap = array_merge($this->eventMap, $aggregate::eventMap());
        }
    }

    /**
     * @param string $aggregateName
     * @return ArrayCollection
     */
    public function findAllEventsByAggregateName(string $aggregateName): ArrayCollection
    {
        return $this->getEventCollectionBy(
            ['aggregateName' => $aggregateName],
            ['id' => 'ASC']
        );
    }

    /**
     * @param $aggregateClass
     * @param string $aggregateId
     * @return Aggregate
     * @throws \Exception
     */
    public function findAggregateById($aggregateClass, string $aggregateId): Aggregate
    {
        /** @var string $aggregateName */
        $aggregateName = $aggregateClass::NAME;

        /** @var ArrayCollection $events */
        $events = $this->findEventsByAggregateId($aggregateName, $aggregateId);

        if ($events->isEmpty()) {
            throw new \Exception('Unknown AggregateId');
        }

        if (!array_key_exists($aggregateName, $this->aggregateMap)) {
            throw new \RuntimeException(sprintf('Missing aggregateType in aggregateMap class %s', \get_class($this)));
        }

        /** @var Aggregate $aggregateClass */
        $aggregate = $aggregateClass::withId($aggregateId);

        /** @var ArrayCollection $events */
        $events = $this->findEventsByAggregateId($aggregateName, $aggregateId);

        foreach ($events->getIterator() as $event) {
            $aggregate->apply($event);
        }

        return $aggregate;
    }

    /**
     * @param string $aggregateName
     * @param string $aggregateId
     * @return ArrayCollection
     */
    public function findEventsByAggregateId(string $aggregateName, string $aggregateId): ArrayCollection
    {
        return $this->getEventCollectionBy(
            [
                'aggregateName' => $aggregateName,
                'aggregateId' => $aggregateId
            ],
            ['version' => 'ASC']
        );
    }

    /**
     * @param DomainEvent $event
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function storeEvent(DomainEvent $event): bool
    {
        $version = $this->eventRepository->getVersion($event->aggregateId());
        $event->withVersion($version);

        if (is_subclass_of($event, DomainEvent::class)) {
            $event = $event->toBaseClass();
        }

        $this->entityManager->persist($event);
        $this->entityManager->flush();
        return true;
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @return ArrayCollection
     */
    private function getEventCollectionBy(array $criteria, array $orderBy = []): ArrayCollection
    {
        $events = new ArrayCollection($this->eventRepository->findBy($criteria, $orderBy));
        return $events->map(function ($event) {
            /** @var DomainEvent $event */
            if (!array_key_exists($event->getEventName(), $this->eventMap)) {
                throw new \RuntimeException(sprintf('Missing eventType in eventMap class %s', \get_class($this)));
            }

            $classname = $this->eventMap[$event->getEventName()];
            $event = $classname::fromBaseClass($event);
            return $event;
        });
    }
}
