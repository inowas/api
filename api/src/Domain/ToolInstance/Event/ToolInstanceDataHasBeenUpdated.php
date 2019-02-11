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

    public const MERGE_STRATEGY_ADD = 0;
    public const MERGE_STRATEGY_REPLACE = 1;
    public const MERGE_STRATEGY_MERGE = 2;
    public const MERGE_STRATEGY_DELETE = 3;

    private $userId;
    private $data;
    private $mergeStrategy;


    /**
     * @param string $userId
     * @param string $aggregateId
     * @param array $data
     * @param int $mergeStrategy
     * @return ToolInstanceDataHasBeenUpdated
     * @throws \Exception
     */
    public static function fromParams(string $userId, string $aggregateId, array $data, int $mergeStrategy = self::MERGE_STRATEGY_REPLACE)
    {
        $self = new self($aggregateId, ToolInstanceAggregate::NAME, self::getEventNameFromClassname(), [
            'user_id' => $userId,
            'data' => $data,
            'merge_strategy' => $mergeStrategy
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

    public function mergeStrategy(): int
    {
        if (null === $this->mergeStrategy) {
            $this->mergeStrategy = $this->payload['merge_strategy'];
        }
        return $this->mergeStrategy;
    }
}
