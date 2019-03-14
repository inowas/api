<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\Modflow\Calculation;

class UpdateModflowModelCalculationCommand extends Command
{

    private $id;
    private $calculation;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/updateModflowModelCalculation.json');
    }

    /**
     * @param array $payload
     * @return self
     */
    public static function fromPayload(array $payload): self
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->calculation = $payload['calculation'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function calculation(): Calculation
    {
        return Calculation::fromArray($this->calculation);
    }
}
