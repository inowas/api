<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\Event;

use App\Domain\ModflowModel\Aggregate\ModflowModelAggregate;
use App\Model\DomainEvent;

final class CalculationHasBeenStarted extends DomainEvent
{

    private $userId;

    /**
     * @param string $userId
     * @param string $modelId
     * @return CalculationHasBeenStarted
     * @throws \Exception
     */
    public static function fromParams(string $userId, string $modelId)
    {
        $self = new self($modelId, ModflowModelAggregate::NAME, self::getEventNameFromClassname(), []);

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
