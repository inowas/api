<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Event;

use App\Model\DomainEvent;
use App\Domain\User\Aggregate\UserAggregate;

final class ToolInstanceHasBeenCloned extends DomainEvent
{

    private $userId;
    private $baseId;

    /**
     * @param string $userId
     * @param string $id
     * @param string $baseId
     * @return ToolInstanceHasBeenCloned
     * @throws \Exception
     */
    public static function fromParams(string $userId, string $id, string $baseId)
    {
        $self = new self($id, UserAggregate::NAME, self::getEventNameFromClassname(), [
            'user_id' => $userId,
            'base_id' => $baseId,
        ]);

        $self->userId = $userId;
        $self->baseId = $baseId;
        return $self;
    }

    public function userId(): string
    {
        if (null === $this->userId) {
            $this->userId = $this->payload['user_id'];
        }
        return $this->userId;
    }

    public function baseId(): string
    {
        if (null === $this->baseId) {
            $this->baseId = $this->payload['base_id'];
        }
        return $this->baseId;
    }
}
