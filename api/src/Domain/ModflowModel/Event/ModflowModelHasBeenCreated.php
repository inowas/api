<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\Event;

use App\Domain\ModflowModel\Aggregate\ModflowModelAggregate;
use App\Model\DomainEvent;

final class ModflowModelHasBeenCreated extends DomainEvent
{

    private $userId;
    private $name;
    private $description;
    private $public;
    private $discretization;

    /**
     * @param string $userId
     * @param string $modelId
     * @param string $name
     * @param string $description
     * @param bool $isPublic
     *
     * @param array $discretization
     * @return ModflowModelHasBeenCreated
     * @throws \Exception
     */
    public static function fromParams(string $userId, string $modelId, string $name, string $description, bool $isPublic, array $discretization)
    {
        $self = new self($modelId, ModflowModelAggregate::NAME, self::getEventNameFromClassname(), [
            'user_id' => $userId,
            'name' => $name,
            'description' => $description,
            'public' => $isPublic,
            'discretization' => $discretization
        ]);

        $self->userId = $userId;
        $self->name = $name;
        $self->description = $description;
        $self->public = $isPublic;
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
        if (null === $this->public) {
            $this->public = $this->payload['public'];
        }
        return $this->public;
    }

    public function discretization(): array
    {
        if (null === $this->discretization) {
            $this->discretization = $this->payload['discretization'];
        }

        return $this->description;
    }
}
