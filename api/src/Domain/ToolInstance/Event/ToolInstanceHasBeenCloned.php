<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Event;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Model\DomainEvent;

final class ToolInstanceHasBeenCloned extends DomainEvent
{

    private $userId;
    private $baseId;
    private $isPublic;

    /**
     * @param string $userId
     * @param string $id
     * @param string $baseId
     * @param bool $isPublic
     * @return ToolInstanceHasBeenCloned
     * @throws \Exception
     */
    public static function fromParams(string $userId, string $id, string $baseId, bool $isPublic)
    {
        $self = new self($id, ToolInstanceAggregate::NAME, self::getEventNameFromClassname(), [
            'user_id' => $userId,
            'base_id' => $baseId,
            'public' => $isPublic
        ]);

        $self->userId = $userId;
        $self->baseId = $baseId;
        $self->isPublic = $isPublic;
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

    public function isPublic(): bool
    {
        if (null === $this->isPublic) {
            $this->isPublic = $this->payload['public'];
        }
        return $this->isPublic;
    }
}
