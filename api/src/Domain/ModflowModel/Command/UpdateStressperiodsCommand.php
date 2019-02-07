<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\Command;

use App\Model\Command;

class UpdateStressperiodsCommand extends Command
{

    private $id;
    private $stressperiods;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/updateStressperiods.json');
    }

    /**
     * @param array $payload
     * @return UpdateStressperiodsCommand
     */
    public static function fromPayload(array $payload): UpdateStressperiodsCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->stressperiods = $payload['stress_periods'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function stressperiods(): array
    {
        return $this->stressperiods;
    }
}
