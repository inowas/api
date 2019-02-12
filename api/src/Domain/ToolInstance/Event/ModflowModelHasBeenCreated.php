<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Event;

use App\Domain\ToolInstance\Aggregate\ToolInstanceAggregate;
use App\Model\DomainEvent;
use App\Model\Modflow\ModflowModel;
use App\Model\ToolMetadata;

final class ModflowModelHasBeenCreated extends DomainEvent
{

    private $userId;
    private $tool;
    private $modflowModel;

    /**
     * @param string $userId
     * @param string $aggregateId
     * @param ModflowModel $modflowModel
     * @return ModflowModelHasBeenCreated
     * @throws \Exception
     */
    public static function fromParams(string $userId, string $aggregateId, ModflowModel $modflowModel): ModflowModelHasBeenCreated
    {
        $self = new self($aggregateId, ToolInstanceAggregate::NAME, self::getEventNameFromClassname(), [
            'user_id' => $userId,
            'modflowModel' => $modflowModel->toArray()
        ]);

        $self->userId = $userId;
        $self->tool = 'T03';
        $self->modflowModel = $modflowModel;
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

    public function modflowModel(): ModflowModel
    {
        if (null === $this->modflowModel) {
            $this->modflowModel = ModflowModel::fromArray($this->payload['modflowModel']);
        }
        return $this->modflowModel;
    }

    public function metadata(): ToolMetadata
    {
        return $this->modflowModel()->getMetadata();
    }
}
