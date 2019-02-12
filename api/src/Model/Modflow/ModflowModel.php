<?php

declare(strict_types=1);

namespace App\Model\Modflow;

use Doctrine\ORM\Mapping as ORM;

final class ModflowModel
{
    private $discretization = [];

    private $soilmodel = [];

    private $boundaries = [];

    private $transport = [];

    private $calculation = [];

    private $optimization = [];

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

    private function __construct()
    {
    }

    /**
     * @return Discretization
     */
    public function getDiscretization(): Discretization
    {
        return Discretization::fromArray($this->discretization);
    }

    /**
     * @param Discretization $discretization
     */
    public function setDiscretization(Discretization $discretization): void
    {
        $this->discretization = $discretization->toArray();
    }

    /**
     * @return Boundaries
     */
    public function getBoundaries(): Boundaries
    {
        return Boundaries::fromArray($this->boundaries);
    }

    /**
     * @param Boundaries $boundaries
     */
    public function setBoundaries(Boundaries $boundaries): void
    {
        $this->boundaries = $boundaries->toArray();
    }

    /**
     * @return Transport
     */
    public function getTransport(): Transport
    {
        return Transport::fromArray($this->transport);
    }

    /**
     * @param Transport $transport
     */
    public function setTransport(Transport $transport): void
    {
        $this->transport = $transport->toArray();
    }

    /**
     * @return Soilmodel
     */
    public function getSoilmodel(): Soilmodel
    {
        return Soilmodel::fromArray($this->soilmodel);
    }

    /**
     * @param Soilmodel $soilmodel
     */
    public function setSoilmodel(Soilmodel $soilmodel): void
    {
        $this->soilmodel = $soilmodel->toArray();
    }

    /**
     * @return Calculation
     */
    public function getCalculation(): Calculation
    {
        return Calculation::fromArray($this->calculation);
    }

    /**
     * @param Calculation $calculation
     */
    public function setCalculation(Calculation $calculation): void
    {
        $this->calculation = $calculation->toArray();
    }

    /**
     * @return Packages
     */
    public function getPackages(): Packages
    {
        return Packages::fromArray($this->packages);
    }

    /**
     * @param Packages $packages
     */
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
