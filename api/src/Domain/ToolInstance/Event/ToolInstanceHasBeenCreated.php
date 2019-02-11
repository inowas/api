<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Event;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Model\DomainEvent;
use App\Model\ToolMetadata;

final class ToolInstanceHasBeenCreated extends DomainEvent
{

    private $userId;
    private $tool;
    private $metadata;
    private $data;

    /**
     * @param string $userId
     * @param string $aggregateId
     * @param string $tool
     * @param ToolMetadata $metadata
     * @param array $data
     * @return ToolInstanceHasBeenCreated
     * @throws \Exception
     */
    public static function fromParams(string $userId, string $aggregateId, string $tool, ToolMetadata $metadata, array $data = [])
    {
        $self = new self($aggregateId, ToolInstanceAggregate::NAME, self::getEventNameFromClassname(), [
            'user_id' => $userId,
            'tool' => $tool,
            'metadata' => $metadata->toArray(),
            'data' => $data
        ]);

        $self->userId = $userId;
        $self->tool = $tool;
        $self->metadata = $metadata;
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

    public function tool(): string
    {
        if (null === $this->tool) {
            $this->tool = $this->payload['tool'];
        }
        return $this->tool;
    }

    public function metadata(): ToolMetadata
    {
        if (null === $this->metadata) {
            $this->metadata = ToolMetadata::fromArray($this->payload['metadata']);
        }
        return $this->metadata;
    }

    public function data(): array
    {
        if (null === $this->data) {
            $this->data = $this->payload['data'];
        }
        return $this->data;
    }

    public function isPublic(): bool
    {
        return ToolMetadata::fromArray($this->payload['metadata'])->isPublic();
    }
}
