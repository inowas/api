<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\Event;

use App\Domain\ModflowModel\Aggregate\ModflowModelAggregate;
use App\Model\DomainEvent;

final class OptimizationStateHasBeenChanged extends DomainEvent
{

    private $userId;
    private $optimization;

    /**
     * @param string $userId
     * @param string $modelId
     * @param array $optimization
     * @return OptimizationStateHasBeenChanged
     * @throws \Exception
     */
    public static function fromParams(string $userId, string $modelId, array $optimization)
    {
        $self = new self($modelId, ModflowModelAggregate::NAME, self::getEventNameFromClassname(), [
            'user_id' => $userId,
            'optimization' => $optimization
        ]);

        $self->userId = $userId;
        $self->optimization = $optimization;
        return $self;
    }

    public function userId(): string
    {
        if (null === $this->userId) {
            $this->userId = $this->payload['user_id'];
        }
        return $this->userId;
    }

    public function optimization(): array
    {
        if (null === $this->optimization) {
            $this->optimization = $this->payload['optimization'];
        }
        return $this->optimization;
    }
}
