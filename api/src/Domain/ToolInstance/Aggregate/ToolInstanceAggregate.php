<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Aggregate;

use App\Model\Aggregate;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCloned;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCreated;

final class ToolInstanceAggregate extends Aggregate
{
    public const NAME = 'toolInstance';

    public static $registeredEvents = [
        ToolInstanceHasBeenCreated::class,
        ToolInstanceHasBeenCloned::class
    ];

    protected $userId;

    protected $isPublic;

    protected function whenToolInstanceHasBeenCloned(ToolInstanceHasBeenCloned $event): void
    {
        $this->aggregateId = $event->aggregateId();
        $this->userId = $event->userId();
        $this->isPublic = $event->isPublic();
    }

    protected function whenToolInstanceHasBeenCreated(ToolInstanceHasBeenCreated $event): void
    {
        $this->aggregateId = $event->aggregateId();
        $this->userId = $event->userId();
        $this->isPublic = $event->isPublic();
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }
}
