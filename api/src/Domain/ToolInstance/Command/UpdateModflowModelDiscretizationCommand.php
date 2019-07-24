<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\Modflow\Discretization;
use Exception;

class UpdateModflowModelDiscretizationCommand extends Command
{

    /** @var string */
    private $id;

    /** @var array */
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
     * @throws Exception
     */
    public static function fromPayload(array $payload): self
    {
        $self = new self();
        $self->id = $payload['id'];

        $self->discretization = [
            'geometry' => $payload['geometry'],
            'bounding_box' => $payload['bounding_box'],
            'grid_size' => $payload['grid_size'],
            'cells' => $payload['cells'],
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
