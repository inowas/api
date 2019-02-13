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
            'geometry' => $payload['geometry'],
            'bounding_box' => $payload['bounding_box'],
            'grid_size' => $payload['grid_size'],
            'active_cells' => $payload['active_cells'],
            'stressperiods' => $payload['stressperiods'],
            'length_unit' => $payload['length_unit'],
            'time_unit' => $payload['time_unit']
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