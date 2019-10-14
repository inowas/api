<?php

declare(strict_types=1);

namespace App\Model\Mcda;

use App\Model\ToolInstance;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\McdaRepository")
 */
class Mcda extends ToolInstance
{
    /**
     * @ORM\Column(name="criteria", type="json_array", nullable=false)
     */
    private $criteria = [];

    /**
     * @ORM\Column(name="weight_assignments", type="json_array", nullable=false)
     */
    private $weightAssignments = [];

    /**
     * @ORM\Column(name="constraints", type="json_array", nullable=false)
     */
    private $constraints = [];

    /**
     * @ORM\Column(name="with_ahp", type="boolean", nullable=false)
     */
    private $withAhp = false;

    /**
     * @ORM\Column(name="suitability", type="json_array", nullable=false)
     */
    private $suitability = [];

    /**
     * @ORM\Column(name="grid_size", type="json_array", nullable=true)
     */
    private $gridSize = null;

    /**
     * @ORM\Column(name="version", type="string", nullable=true)
     */
    private $version = '0';

    public function critera(): array
    {
        return $this->criteria;
    }

    public function findCriterion(string $id): ?Criterion
    {
        if (array_key_exists($id, $this->critera())) {
            return Criterion::fromArray($this->criteria[$id]);
        }

        return null;
    }

    public function weightAssignments(): array
    {
        return $this->weightAssignments;
    }

    public function constraints(): array
    {
        return $this->constraints;
    }

    public function withAhp(): bool
    {
        return $this->withAhp;
    }

    public function suitability(): array
    {
        return $this->suitability;
    }

    public function gridSize(): ?array
    {
        return $this->gridSize;
    }

    public function version(): string
    {
        if (null === $this->version) {
            $this->version = '0';
        }
        return $this->version;
    }

    /**
     * @param int $version
     */
    public function setVersion(int $version): void
    {
        $this->version = $version;
    }


    public function data(): array
    {
        return [
            'version' => $this->version(),
            'grid_size' => $this->gridSize(),
            'criteria' => array_values($this->critera()),
            'constraints' => $this->constraints(),
            'suitability' => $this->suitability(),
            'weight_assignments' => $this->weightAssignments(),
            'with_ahp' => $this->withAhp(),
        ];
    }

    public function setData(array $data): void
    {
        $this->version = $data['version'] ?? $this->version;
        $this->gridSize = $data['grid_size'] ?? $this->gridSize;
        $this->criteria = $data['criteria'] ?? $this->criteria;
        $this->constraints = $data['constraints'] ?? $this->constraints;
        $this->suitability = $data['suitability'] ?? $this->suitability;
        $this->weightAssignments = $data['weight_assignments'] ?? $this->weightAssignments;
        $this->withAhp = $data['with_ahp'] ?? $this->withAhp;
    }
}
