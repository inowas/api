<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\Event;

use App\Domain\ModflowModel\Aggregate\ModflowModelAggregate;
use App\Model\DomainEvent;

final class CalculationStateHasBeenChanged extends DomainEvent
{

    private $userId;
    private $calculation;

    /**
     * @param string $userId
     * @param string $modelId
     * @param array $calculation
     * @return CalculationStateHasBeenChanged
     * @throws \Exception
     */
    public static function fromParams(string $userId, string $modelId, array $calculation)
    {
        $self = new self($modelId, ModflowModelAggregate::NAME, self::getEventNameFromClassname(), [
            'user_id' => $userId,
            'calculation' => $calculation
        ]);

        $self->userId = $userId;
        $self->calculation = $calculation;
        return $self;
    }

    public function userId(): string
    {
        if (null === $this->userId) {
            $this->userId = $this->payload['user_id'];
        }
        return $this->userId;
    }

    public function calculation(): array
    {
        if (null === $this->calculation) {
            $this->calculation = $this->payload['calculation'];
        }
        return $this->calculation;
    }
}
