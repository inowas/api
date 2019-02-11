<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Aggregate;

use App\Model\Aggregate;
use App\Domain\ToolInstance\Event\ToolInstanceDataHasBeenUpdated;
use App\Domain\ToolInstance\Event\ToolInstanceMetadataHasBeenUpdated;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCloned;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCreated;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenDeleted;

final class ToolInstanceAggregate extends Aggregate
{
    public const NAME = 'toolInstance';

    public static $registeredEvents = [
        ToolInstanceHasBeenCreated::class,
        ToolInstanceHasBeenCloned::class,
        ToolInstanceHasBeenDeleted::class,
        ToolInstanceDataHasBeenUpdated::class,
        ToolInstanceMetadataHasBeenUpdated::class
    ];

    private $tool;

    protected $userId;

    protected $isPublic;

    protected function whenToolInstanceHasBeenCreated(ToolInstanceHasBeenCreated $event): void
    {
        $this->aggregateId = $event->aggregateId();
        $this->userId = $event->userId();
        $this->isPublic = $event->isPublic();
        $this->tool = $event->tool();
    }

    protected function whenToolInstanceHasBeenCloned(ToolInstanceHasBeenCloned $event): void
    {
        $this->aggregateId = $event->aggregateId();
        $this->userId = $event->userId();
        $this->isPublic = $event->isPublic();
    }

    public function getTool(): string
    {
        return $this->tool;
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
