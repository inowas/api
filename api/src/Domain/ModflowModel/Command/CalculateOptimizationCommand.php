<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\Command;

use App\Model\Command;

final class CalculateOptimizationCommand extends Command
{

    private $id;
    private $optimizationId;
    private $isInitial;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/calculateOptimization.json');
    }

    /**
     * @param array $payload
     * @return CalculateOptimizationCommand
     */
    public static function fromPayload(array $payload): CalculateOptimizationCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->optimizationId = $payload['optimization_id'];
        $self->isInitial = $payload['is_initial'];
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

    public function isInitial(): bool
    {
        return $this->isInitial;
    }


}
