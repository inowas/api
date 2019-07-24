<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use Exception;

class DeleteScenarioAnalysisCommand extends Command
{
    /** @var string */
    private $id;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/deleteScenarioAnalysis.json');
    }

    /**
     * @param array $payload
     * @return DeleteScenarioAnalysisCommand
     * @throws Exception
     */
    public static function fromPayload(array $payload): DeleteScenarioAnalysisCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }
}
