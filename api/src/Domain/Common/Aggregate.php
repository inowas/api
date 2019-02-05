<?php

declare(strict_types=1);

namespace App\Domain\Common;

use App\Entity\Event;

abstract class Aggregate
{

    public const NAME = '';

    /**
     * @var string
     */
    protected $id;

    public static $registeredEvents = [];

    public static function withId(string $id): Aggregate
    {
        $self = new static();
        $self->id = $id;
        return $self;
    }

    public static function eventMap(): array
    {
        $eventMap = [];
        foreach (static::$registeredEvents as $classname) {
            $eventMap[$classname::getEventNameFromClassname()] = $classname;
        }

        return $eventMap;
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
     */
    public function name(): string
    {
        return self::NAME;
    }

    public function apply(Event $e): void
    {
        if (!in_array(get_class($e), static::$registeredEvents)){
            throw new \InvalidArgumentException(sprintf('Class %s is not in the list of registeredEvents', get_class($e)));
        }
        $handler = $this->determineEventMethodFor($e);
        if (method_exists($this, $handler)) {
            $this->{$handler}($e);
        }
    }

    protected function determineEventMethodFor(Event $e): string
    {
        return 'when' . implode(\array_slice(explode('\\', \get_class($e)), -1));
    }
}
