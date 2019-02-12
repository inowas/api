<?php

declare(strict_types=1);

namespace App\Model\Modflow;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Model\ToolMetadata;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="modflowmodel_instances")
 *
 * @ApiResource(attributes={"access_control"="is_granted('ROLE_USER')"})
 */
final class ModflowModel
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="string", unique=true, nullable=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="user_id", type="string", length=36, nullable=false)
     */
    private $userId;

    /**
     * @var array
     *
     * @ORM\Column(name="metadata", type="json_array")
     */
    private $metadata = [];

    /**
     * @var array
     *
     * @ORM\Column(name="discretization", type="json_array")
     */
    private $discretization = [];

    /**
     * @var array
     *
     * @ORM\Column(name="soilmodel", type="json_array")
     */
    private $soilmodel = [];

    /**
     * @var array
     *
     * @ORM\Column(name="boundaries", type="json_array")
     */
    private $boundaries = [];

    /**
     * @var array
     *
     * @ORM\Column(name="transport", type="json_array")
     */
    private $transport = [];

    /**
     * @var array
     *
     * @ORM\Column(name="calculation", type="json_array")
     */
    private $calculation = [];

    /**
     * @var array
     *
     * @ORM\Column(name="optimization", type="json_array")
     */
    private $optimization = [];

    /**
     * @var array
     *
     * @ORM\Column(name="packages", type="json_array")
     */
    private $packages = [];

    /**
     * @param string $id
     * @param string $userId
     * @return ModflowModel
     */
    public static function fromParams(string $id, string $userId): ModflowModel
    {
        $self = new self();
        $self->id = $id;
        $self->userId = $userId;
        return $self;
    }

    public static function fromArray(array $arr): ModflowModel
    {
        $self = new self();
        $self->id = $arr['id'];
        $self->userId = $arr['user_id'];
        $self->metadata = $arr['metadata'] ?? [];
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
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return ToolMetadata
     */
    public function getMetadata(): ToolMetadata
    {
        return ToolMetadata::fromArray($this->metadata);
    }

    /**
     * @param ToolMetadata $metadata
     */
    public function setMetadata(ToolMetadata $metadata): void
    {
        $this->metadata = $metadata->toArray();
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
     * @return array
     */
    public function getBoundaries(): array
    {
        return $this->boundaries;
    }

    /**
     * @param array $boundaries
     */
    public function setBoundaries(array $boundaries): void
    {
        $this->boundaries = $boundaries;
    }

    /**
     * @return array
     */
    public function getTransport(): array
    {
        return $this->transport;
    }

    /**
     * @param array $transport
     */
    public function setTransport(array $transport): void
    {
        $this->transport = $transport;
    }

    /**
     * @return array
     */
    public function getSoilmodel(): array
    {
        return $this->soilmodel;
    }

    /**
     * @param array $soilmodel
     */
    public function setSoilmodel(array $soilmodel): void
    {
        $this->soilmodel = $soilmodel;
    }

    /**
     * @return array
     */
    public function getCalculation(): array
    {
        return $this->calculation;
    }

    /**
     * @param array $calculation
     */
    public function setCalculation(array $calculation): void
    {
        $this->calculation = $calculation;
    }

    /**
     * @return array
     */
    public function getPackages(): array
    {
        return $this->packages;
    }

    /**
     * @param array $packages
     */
    public function setPackages(array $packages): void
    {
        $this->packages = $packages;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return array
     */
    public function getOptimization(): array
    {
        return $this->optimization;
    }

    /**
     * @param array $optimization
     */
    public function setOptimization(array $optimization): void
    {
        $this->optimization = $optimization;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'metadata' => $this->metadata,
            'discretization' => $this->discretization,
            'boundaries' => $this->boundaries,
            'transport' => $this->transport,
            'calculation' => $this->calculation,
            'optimization' => $this->optimization,
            'packages' => $this->packages
        ];
    }
}
