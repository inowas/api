<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Event;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Model\DomainEvent;

/**
 * Class ToolInstanceDataHasBeenUpdated
 * @package App\Domain\ToolInstance\Event
 *
 */
final class ToolInstanceDataHasBeenUpdated extends DomainEvent
{

    private $userId;
    private $data;

    /**
     * @param string $userId
     * @param string $aggregateId
     * @param array $data
     * @return ToolInstanceDataHasBeenUpdated
     * @throws \Exception
     */
    public static function fromParams(string $userId, string $aggregateId, array $data)
    {
        $self = new self($aggregateId, ToolInstanceAggregate::NAME, self::getEventNameFromClassname(), [
            'user_id' => $userId,
            'data' => $data
        ]);

        $self->userId = $userId;
        $self->data = $data;
        return $self;
    }

    public function userId(): string
    {
        if (null === $this->userId) {
            $this->userId = $this->payload['user_id'];
        }
        return $this->userId;
    }

    public function data(): array
    {
        if (null === $this->data) {
            $this->data = $this->payload['data'];
        }
        return $this->data;
    }
}
