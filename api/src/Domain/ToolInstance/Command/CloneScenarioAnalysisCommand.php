<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;

class CloneScenarioAnalysisCommand extends Command
{

    private $id;
    private $newId;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/cloneScenarioAnalysis.json');
    }

    /**
     * @param array $payload
     * @return CloneScenarioAnalysisCommand
     * @throws \Exception
     */
    public static function fromPayload(array $payload): CloneScenarioAnalysisCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->newId = $payload['new_id'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function newId(): string
    {
        return $this->newId;
    }
}
