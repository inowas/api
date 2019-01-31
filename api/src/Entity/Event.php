<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * Class Event
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 * @ORM\Table(name="events")
 * @ORM\HasLifecycleCallbacks
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Uuid
     * @ORM\Column(type="string", length=36)
     */
    protected $aggregateId;

    /**
     * @var Uuid
     * @ORM\Column(type="string", length=64)
     */
    protected $aggregateName;

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
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
     * @var \datetime $created
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @return string
     */
    public static function eventName(): string
    {
        return lcfirst(static::class);
    }

    /**
     * @param Event $event
     * @return Event
     * @throws \Exception
     */
    public static function fromBaseClass(Event $event) {
        $self = new static($event->aggregateId(), $event->aggregateName(), $event->eventName(), $event->payload());
        return $self;
    }

    /**
     * Event constructor.
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
     * @return int
     */
    public function version(): int
    {
        return $this->version;
    }

    public function withVersion(int $version): self
    {
        $this->version = $version;
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
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function onPrePersist(): void
    {
        $this->created = new \DateTime('now');
    }

    /**
     * @return Event
     * @throws \Exception
     */
    public function toBaseClass(): Event
    {
        $self = new Event($this->aggregateId(), $this->aggregateName(), $this->eventName(), $this->payload());
        $self->id = $this->id;
        $self->version = $this->version;
        return $self;
    }
}
