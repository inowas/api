<?php

declare(strict_types=1);

namespace App\Model;

use ApiPlatform\Core\Annotation\ApiResource;
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="user_id", type="string", length=36, nullable=false)
     */
    private $userId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean")
     */
    private $isPublic;

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
     * @var \DateTimeImmutable $created
     *
     * @ORM\Column(name="created_at", type="datetime_immutable")
     */
    protected $createdAt;

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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getDiscretization(): array
    {
        return $this->discretization;
    }

    /**
     * @param array $discretization
     */
    public function setDiscretization(array $discretization): void
    {
        $this->discretization = $discretization;
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
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    /**
     * @param bool $isPublic
     */
    public function setIsPublic(bool $isPublic): void
    {
        $this->isPublic = $isPublic;
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

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeImmutable $createdAt
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

}
