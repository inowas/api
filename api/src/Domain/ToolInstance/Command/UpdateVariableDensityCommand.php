<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\Modflow\VariableDensity;
use Exception;

class UpdateVariableDensityCommand extends Command
{
    /** @var string */
    private $id;

    /** @var array */
    private $variableDensity;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/updateVariableDensity.json');
    }

    /**
     * @param array $payload
     * @return self
     * @throws Exception
     */
    public static function fromPayload(array $payload): self
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->variableDensity = $payload['variableDensity'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function variableDensity(): VariableDensity
    {
        return VariableDensity::fromArray($this->variableDensity);
    }
}
