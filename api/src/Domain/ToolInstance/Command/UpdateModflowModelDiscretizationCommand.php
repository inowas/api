<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\Modflow\Discretization;

class UpdateModflowModelDiscretizationCommand extends Command
{

    private $id;
    private $discretization;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/updateModflowModelDiscretization.json');
    }

    /**
     * @param array $payload
     * @return self
     */
    public static function fromPayload(array $payload): self
    {
        $self = new self();
        $self->id = $payload['id'];

        $self->discretization = [
            'geometry' => $payload['geometry'] ?? null,
            'bounding_box' => $payload['bounding_box'] ?? null,
            'grid_size' => $payload['grid_size'] ?? null,
            'active_cells' => $payload['active_cells'] ?? null,
            'stressperiods' => $payload['stressperiods'] ?? null,
            'length_unit' => $payload['length_unit'] ?? null,
            'time_unit' => $payload['time_unit'] ?? null
        ];

        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function discretization(): Discretization
    {
        return Discretization::fromArray($this->discretization);
    }
}
