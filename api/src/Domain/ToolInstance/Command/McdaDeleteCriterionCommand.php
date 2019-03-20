<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;

class McdaDeleteCriterionCommand extends Command
{

    private $id;
    private $criterionId;

    /**
     * @return string
     */
    public static function getJsonSchema(): string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/mcdaDeleteCriterion.json');
    }

    /**
     * @param array $payload
     * @return self
     * @throws \Exception
     */
    public static function fromPayload(array $payload): self
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->criterionId = $payload['criterion_id'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function criterionId(): string
    {
        return $this->criterionId;
    }
}
