<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;

class DeleteScenarioCommand extends Command
{

    private $id;
    private $scenarioId;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/deleteScenario.json');
    }

    /**
     * @param array $payload
     * @return DeleteScenarioCommand
     * @throws \Exception
     */
    public static function fromPayload(array $payload): DeleteScenarioCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->scenarioId = $payload['scenario_id'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function scenarioId(): string
    {
        return $this->scenarioId;
    }
}
