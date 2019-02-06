<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Event;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Model\DomainEvent;

final class ToolInstanceHasBeenDeleted extends DomainEvent
{

    private $userId;

    /**
     * @param string $userId
     * @param string $aggregateId
     * @return ToolInstanceHasBeenDeleted
     * @throws \Exception
     */
    public static function fromParams(string $userId, string $aggregateId)
    {
        $self = new self($aggregateId, ToolInstanceAggregate::NAME, self::getEventNameFromClassname(), [
            'user_id' => $userId
        ]);

        $self->userId = $userId;
        return $self;
    }

    public function userId(): string
    {
        if (null === $this->userId) {
            $this->userId = $this->payload['user_id'];
        }
        return $this->userId;
    }
}
