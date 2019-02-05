<?php

declare(strict_types=1);

namespace App\Domain\Common;

use Doctrine\Common\Collections\ArrayCollection;

abstract class Projector
{

    abstract public function aggregateName(): string;

    abstract protected function truncateTable(): void;

    public function apply(DomainEvent $e): void
    {
        $this->onEvent($e);
    }

    public function recreateFromHistory(ArrayCollection $events): void
    {
        $this->truncateTable();
        foreach ($events->getIterator() as $event) {
            $this->onEvent($event);
        }
    }

    protected function onEvent(DomainEvent $e): void
    {
        $handler = $this->determineEventMethodFor($e);
        if (!method_exists($this, $handler)) {
            throw new \RuntimeException(sprintf(
                'Missing event method %s for projector %s', $handler, \get_class($this)
            ));
        }
        $this->{$handler}($e);
    }

    protected function determineEventMethodFor(DomainEvent $e): string
    {
        return 'on' . implode(\array_slice(explode('\\', \get_class($e)), -1));
    }
}