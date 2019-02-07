<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\Command;

use App\Model\Command;

class CancelOptimizationCalculationCommand extends Command
{

    private $id;
    private $optimizationId;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/cancelOptimizationCalculation.json');
    }

    /**
     * @param array $payload
     * @return CancelOptimizationCalculationCommand
     */
    public static function fromPayload(array $payload): CancelOptimizationCalculationCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->optimizationId = $payload['optimization_id'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function optimizationId(): string
    {
        return $this->optimizationId;
    }
}
