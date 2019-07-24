<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\Modflow\Boundary\BoundaryCollection;
use App\Model\Modflow\Boundary\BoundaryFactory;
use App\Model\Modflow\Discretization;
use App\Model\Modflow\Layer;
use App\Model\Modflow\Soilmodel;
use Exception;

class ImportModflowModelCommand extends Command
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var bool */
    private $isPublic;

    /** @var array */
    private $discretization;

    /** @var array */
    private $soilmodel;

    /** @var array */
    private $boundaries;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/importModflowModel.json');
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
        $self->name = $payload['name'];
        $self->description = $payload['description'];
        $self->isPublic = $payload['public'];
        $self->discretization = $payload['discretization'];
        $self->soilmodel = $payload['soilmodel'];
        $self->boundaries = $payload['boundaries'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function discretization(): Discretization
    {
        return Discretization::fromArray($this->discretization);
    }

    public function soilmodel(): Soilmodel
    {
        $soilmodel = Soilmodel::create();
        foreach ($this->soilmodel['layers'] as $layer) {
            $soilmodel->addLayer(Layer::fromArray($layer));
        }

        return $soilmodel;
    }

    /**
     * @return BoundaryCollection
     * @throws Exception
     */
    public function boundaries(): BoundaryCollection
    {
        $boundaries = BoundaryCollection::create();
        foreach ($this->boundaries as $boundary) {
            $boundaries->addBoundary(BoundaryFactory::fromArray($boundary));
        }

        return $boundaries;
    }
}
