<?php

declare(strict_types=1);

namespace App\Model\Modflow;

use App\Model\Modflow\Boundary\BoundaryCollection;
use App\Model\ToolInstance;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ModflowModel extends ToolInstance
{

    /**
     * @ORM\Column(name="discretization", type="json_array", nullable=false)
     */
    private $discretization = [];

    /**
     * @ORM\Column(name="soilmodel", type="json_array", nullable=false)
     */
    private $soilmodel = [];

    /**
     * @ORM\Column(name="boundaries", type="json_array", nullable=false)
     */
    private $boundaries = [];

    /**
     * @ORM\Column(name="packages", type="json_array", nullable=false)
     */
    private $packages = [];

    public static function create(): ModflowModel
    {
        return new self();
    }

    public static function fromArray(array $arr): ModflowModel
    {
        $self = new self();
        $self->discretization = $arr['discretization'] ?? [];
        $self->soilmodel = $arr['soilmodel'] ?? [];
        $self->boundaries = $arr['boundaries'] ?? [];
        $self->packages = $arr['packages'] ?? [];
        return $self;
    }

    public function discretization(): Discretization
    {
        return Discretization::fromArray($this->discretization);
    }

    public function setDiscretization(Discretization $discretization): void
    {
        $this->discretization = $discretization->toArray();
    }

    public function boundaries(): BoundaryCollection
    {
        return BoundaryCollection::fromArray($this->boundaries);
    }

    public function setBoundaries(BoundaryCollection $boundaries): void
    {
        $this->boundaries = $boundaries->toArray();
    }

    public function packages(): Packages
    {
        return Packages::fromArray($this->packages);
    }

    public function setPackages(Packages $packages): void
    {
        $this->packages = $packages->toArray();
    }

    public function soilmodel(): Soilmodel
    {
        return Soilmodel::fromArray($this->soilmodel);
    }

    public function setSoilmodel(Soilmodel $soilmodel): void
    {
        $this->soilmodel = $soilmodel->toArray();
    }

    public function data(): array
    {
        return ['discretization' => $this->discretization];
    }

    public function setData(array $data): void
    {
        $this->setDiscretization(Discretization::fromArray($data));
    }

    public function toArray(): array
    {
        return [
            'discretization' => $this->discretization,
            'soilmodel' => $this->soilmodel,
            'boundaries' => $this->boundaries,
            'packages' => $this->packages
        ];
    }
}
