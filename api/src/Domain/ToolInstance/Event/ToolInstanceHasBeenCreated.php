<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Event;

use App\Domain\Common\DomainEvent;
use App\Domain\User\Aggregate\UserAggregate;

final class ToolInstanceHasBeenCreated extends DomainEvent
{

    private $userId;
    private $tool;
    private $name;
    private $description;
    private $isPublic;
    private $data;

    /**
     * @param string $userId
     * @param string $aggregateId
     * @param string $tool
     * @param string $name
     * @param string $description
     * @param bool $isPublic
     * @param array $data
     * @return ToolInstanceHasBeenCreated
     * @throws \Exception
     */
    public static function fromParams(string $userId, string $aggregateId, string $tool, string $name, string $description, bool $isPublic, array $data = [])
    {
        $self = new self($aggregateId, UserAggregate::NAME, self::getEventNameFromClassname(), [
            'user_id' => $userId,
            'tool' => $tool,
            'name' => $name,
            'description' => $description,
            'public' => $isPublic,
            'data' => $data
        ]);

        $self->userId = $userId;
        $self->tool = $tool;
        $self->name = $name;
        $self->description = $description;
        $self->isPublic = $isPublic;
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

    public function name(): string
    {
        if (null === $this->name) {
            $this->name = $this->payload['name'];
        }
        return $this->name;
    }

    public function description(): string
    {
        if (null === $this->description) {
            $this->description = $this->payload['description'];
        }
        return $this->description;
    }

    public function isPublic(): bool
    {
        if (null === $this->isPublic) {
            $this->isPublic = $this->payload['public'];
        }
        return $this->isPublic;
    }

    public function data(): array
    {
        if (null === $this->data) {
            $this->data = $this->payload['data'];
        }
        return $this->data;
    }
}
