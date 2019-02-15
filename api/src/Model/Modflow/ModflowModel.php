<?php

declare(strict_types=1);

namespace App\Model\Modflow;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Model\ToolInstance;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ApiResource(
 *     collectionOperations={},
 *     itemOperations={"get"},
 *     attributes={"access_control"="is_granted('ROLE_USER')"}
 *     )
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
     * @ORM\Column(name="transport", type="json_array", nullable=false)
     */
    private $transport = [];

    /**
     * @ORM\Column(name="calculation", type="json_array", nullable=false)
     */
    private $calculation = [];

    /**
     * @ORM\Column(name="optimization", type="json_array", nullable=false)
     */
    private $optimization = [];

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
        $self->boundaries = $arr['boundaries'] ?? [];
        $self->transport = $arr['transport'] ?? [];
        $self->calculation = $arr['calculation'] ?? [];
        $self->optimization = $arr['optimization'] ?? [];
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

    public function boundaries(): Boundaries
    {
        return Boundaries::fromArray($this->boundaries);
    }

    public function setBoundaries(Boundaries $boundaries): void
    {
        $this->boundaries = $boundaries->toArray();
    }

    public function transport(): Transport
    {
        return Transport::fromArray($this->transport);
    }

    public function setTransport(Transport $transport): void
    {
        $this->transport = $transport->toArray();
    }

    public function soilmodel(): Soilmodel
    {
        return Soilmodel::fromArray($this->soilmodel);
    }

    public function setSoilmodel(Soilmodel $soilmodel): void
    {
        $this->soilmodel = $soilmodel->toArray();
    }

    public function getCalculation(): Calculation
    {
        return Calculation::fromArray($this->calculation);
    }

    public function setCalculation(Calculation $calculation): void
    {
        $this->calculation = $calculation->toArray();
    }

    public function getPackages(): Packages
    {
        return Packages::fromArray($this->packages);
    }

    public function setPackages(Packages $packages): void
    {
        $this->packages = $packages->toArray();
    }

    public function toArray(): array
    {
        return [
            'discretization' => $this->discretization,
            'boundaries' => $this->boundaries,
            'transport' => $this->transport,
            'calculation' => $this->calculation,
            'packages' => $this->packages
        ];
    }
}
