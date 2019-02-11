<?php

declare(strict_types=1);

namespace App\Model\Modflow;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Model\ToolMetadata;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="modflow_models")
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


    public function __clone()
    {
        $this->id = null;
    }

    /**
     * @param string $id
     * @return ModflowModel
     */
    public static function createFromId(string $id): ModflowModel
    {
        return new self($id);
    }

    /**
     * ModflowModel constructor.
     * @param string $id
     */
    private function __construct(string $id)
    {
        $this->id = $id;
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
}
