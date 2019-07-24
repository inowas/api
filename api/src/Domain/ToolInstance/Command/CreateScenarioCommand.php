<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use Exception;

class CreateScenarioCommand extends Command
{

    /** @var string */
    private $id;

    /** @var string */
    private $basemodelId;

    /** @var string */
    private $scenarioId;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/createScenario.json');
    }

    /**
     * @param array $payload
     * @return CreateScenarioCommand
     * @throws Exception
     */
    public static function fromPayload(array $payload): CreateScenarioCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->basemodelId = $payload['basemodel_id'];
        $self->scenarioId = $payload['scenario_id'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function basemodelId(): string
    {
        return $this->basemodelId;
    }

    public function scenarioId(): string
    {
        return $this->scenarioId;
    }
}
