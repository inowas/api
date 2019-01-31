<?php

declare(strict_types=1);

namespace App\Model\Common;

use App\Entity\Event;

abstract class Projection
{

    abstract public function aggregateName(): string;

    abstract protected function truncateTable(): void;

    public function apply(Event $e): void
    {
        $this->onEvent($e);
    }

    public function recreateFromHistory(array $events): void
    {
        $this->truncateTable();
        foreach ($events as $event) {
            $this->onEvent($event);
        }
    }

    protected function onEvent(Event $e): void
    {
        $handler = $this->determineEventMethodFor($e);
        if (!method_exists($this, $handler)) {
            throw new \RuntimeException(sprintf(
                'Missing event method %s for projector %s', $handler, \get_class($this)
            ));
        }
        $this->{$handler}($e);
    }

    protected function determineEventMethodFor(Event $e): string
    {
        return 'on' . implode(\array_slice(explode('\\', \get_class($e)), -1));
    }
}
