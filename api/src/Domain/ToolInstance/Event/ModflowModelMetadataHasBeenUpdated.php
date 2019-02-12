<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Event;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Model\DomainEvent;
use App\Model\ToolMetadata;

final class ModflowModelMetadataHasBeenUpdated extends DomainEvent
{

    private $userId;
    private $metadata;

    /**
     * @param string $userId
     * @param string $aggregateId
     * @param ToolMetadata $metadata
     * @return ModflowModelMetadataHasBeenUpdated
     * @throws \Exception
     */
    public static function fromParams(string $userId, string $aggregateId, ToolMetadata $metadata): ModflowModelMetadataHasBeenUpdated
    {
        $self = new self($aggregateId, ToolInstanceAggregate::NAME, self::getEventNameFromClassname(), [
            'user_id' => $userId,
            'metadata' => $metadata->toArray(),
        ]);

        $self->userId = $userId;
        $self->metadata = $metadata;
        return $self;
    }

    public function userId(): string
    {
        if (null === $this->userId) {
            $this->userId = $this->payload['user_id'];
        }
        return $this->userId;
    }

    public function metadata(): ToolMetadata
    {
        if (null === $this->metadata) {
            $this->metadata = ToolMetadata::fromArray($this->payload['metadata']);
        }
        return $this->metadata;
    }
}
