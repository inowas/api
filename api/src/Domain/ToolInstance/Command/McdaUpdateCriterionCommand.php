<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\Mcda\Criterion;

class McdaUpdateCriterionCommand extends Command
{

    private $id;
    private $criterion;

    /**
     * @return string
     */
    public static function getJsonSchema(): string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/mcdaUpdateCriterion.json');
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
        $self->criterion = $payload['criterion'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function criterion(): Criterion
    {
        return Criterion::fromArray($this->criterion);
    }
}
