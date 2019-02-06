<?php

declare(strict_types=1);

namespace App\Model;

abstract class Aggregate
{

    public const NAME = '';

    /**
     * @var string
     */
    protected $aggregateId;

    public static $registeredEvents = [];

    public static function eventMap(): array
    {
        $eventMap = [];
        foreach (static::$registeredEvents as $classname) {
            $eventMap[$classname::getEventNameFromClassname()] = $classname;
        }

        return $eventMap;
    }

    public static function withId(string $id): Aggregate
    {
        $self = new static();
        $self->aggregateId = $id;
        return $self;
    }

    protected function __construct()
    {
    }

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function apply(DomainEvent $e): void
    {
        if (!in_array(get_class($e), static::$registeredEvents)) {
            throw new \InvalidArgumentException(sprintf('Class %s is not in the list of registeredEvents', get_class($e)));
        }
        $handler = $this->determineEventMethodFor($e);
        if (method_exists($this, $handler)) {
            $this->{$handler}($e);
        }
    }

    protected function determineEventMethodFor(DomainEvent $e): string
    {
        return 'when' . implode(\array_slice(explode('\\', \get_class($e)), -1));
    }
}
