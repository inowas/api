<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\Aggregate;

use App\Model\Aggregate;

final class ModflowModelAggregate extends Aggregate
{
    public const NAME = 'modflowModel';

    public static $registeredEvents = [
    ];

    protected $userId;

    protected $isPublic;

    protected function whenModflowModelHasBeenCloned( $event): void
    {
        $this->aggregateId = $event->aggregateId();
        $this->userId = $event->userId();
        $this->isPublic = $event->isPublic();
    }

    protected function whenModflowModelHasBeenCreated( $event): void
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
