<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\Command;

use App\Model\Command;

class CancelModflowModelCalculationCommand extends Command
{

    private $id;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/cancelModflowModelCalculation.json');
    }

    /**
     * @param array $payload
     * @return CancelModflowModelCalculationCommand
     */
    public static function fromPayload(array $payload): CancelModflowModelCalculationCommand
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
