<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * Class DomainEvent
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 * @ORM\Table(name="events")
 * @ORM\HasLifecycleCallbacks
 */
class DomainEvent
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Uuid
     * @ORM\Column(type="string", length=36, nullable=false)
     */
    protected $aggregateId;

    /**
     * @var Uuid
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    protected $aggregateName;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    protected $eventName;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $version;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    protected $payload = [];

    /**
     * @var \DateTimeImmutable $createdAt
     *
     * @ORM\Column(type="datetime_immutable")
     */
    protected $createdAt;

    /**
     * @return string
     */
    public static function getEventNameFromClassname(): string
    {
        return lcfirst(substr(static::class, strrpos(static::class, '\\') + 1));
    }

    /**
     * @param DomainEvent $event
     * @return DomainEvent
     * @throws \Exception
     */
    public static function fromBaseClass(DomainEvent $event) {
        $self = new static($event->aggregateId(), $event->aggregateName(), $event->getEventNameFromClassname(), $event->payload());
        return $self;
    }

    /**
     * DomainEvent constructor.
     * @param string $aggregateId
     * @param string $aggregateName
     * @param string $eventName
     * @param array $payload
     * @throws \Exception
     */
    protected function __construct(string $aggregateId, string $aggregateName, string $eventName, array $payload)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->aggregateId = $aggregateId;
        $this->aggregateName = $aggregateName;
        $this->eventName = $eventName;
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return string
     *
     */
    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    /**
     * @return string
     *
     */
    public function aggregateName(): string
    {
        return $this->aggregateName;
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * @return int
     */
    public function version(): int
    {
        return $this->version;
    }

    /**
     * @param int $version
     * @return DomainEvent
     * @throws \Exception
     */
    public function withVersion(int $version): self
    {
        $this->version = $version;
        $this->createdAt = new \DateTimeImmutable('now');
        return $this;
    }

    /**
     * @return array
     */
    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable('now');
    }

    /**
     * @return DomainEvent
     * @throws \Exception
     */
    public function toBaseClass(): DomainEvent
    {
        $self = new DomainEvent($this->aggregateId(), $this->aggregateName(), $this->getEventNameFromClassname(), $this->payload());
        $self->id = $this->id;
        $self->version = $this->version;
        return $self;
    }
}
