<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Event;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Model\DomainEvent;
use App\Model\Modflow\Discretization;
use App\Model\ToolMetadata;

final class ModflowModelHasBeenCreated extends DomainEvent
{

    private $userId;
    private $metadata;
    private $discretization;

    /**
     * @param string $userId
     * @param string $modelId
     * @param ToolMetadata $metadata
     * @param Discretization $discretization
     * @return ModflowModelHasBeenCreated
     * @throws \Exception
     */
    public static function fromParams(string $userId, string $modelId, ToolMetadata $metadata, Discretization $discretization): ModflowModelHasBeenCreated
    {
        $self = new self($modelId, ToolInstanceAggregate::NAME, self::getEventNameFromClassname(), [
            'user_id' => $userId,
            'metadata' => $metadata->toArray(),
            'discretization' => $discretization->toArray()
        ]);

        $self->userId = $userId;
        $self->metadata = $metadata;
        $self->discretization = $discretization;
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

    public function discretization(): Discretization
    {
        if (null === $this->discretization) {
            $this->discretization = Discretization::fromArray($this->payload['discretization']);
        }
        return $this->discretization;
    }

    public function isPublic(): bool
    {
        return ToolMetadata::fromArray($this->payload['metadata'])->isPublic();
    }

    public function tool(): string
    {
        return 'T03';
    }
}
