<?php

declare(strict_types=1);

namespace App\Model\Common;

use App\Entity\Event;

abstract class Aggregate
{

    public const NAME = '';

    /**
     * @var string
     */
    protected $id;

    public static function withId(string $id): Aggregate
    {
        $self = new static();
        $self->id = $id;
        return $self;
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
