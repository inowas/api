<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\ToolMetadata;
use Exception;

class CreateScenarioAnalysisCommand extends Command
{

    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var bool */
    private $public;

    /** @var string */
    private $basemodelId;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/createScenarioAnalysis.json');
    }

    /**
     * @param array $payload
     * @return CreateScenarioAnalysisCommand
     * @throws Exception
     */
    public static function fromPayload(array $payload): CreateScenarioAnalysisCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->name = $payload['name'];
        $self->description = $payload['description'];
        $self->public = $payload['public'];
        $self->basemodelId = $payload['basemodel_id'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function toolMetadata(): ToolMetadata
    {
        return ToolMetadata::fromParams($this->name, $this->description, $this->public);
    }

    public function basemodelId(): string
    {
        return $this->basemodelId;
    }
}
