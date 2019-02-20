<?php

declare(strict_types=1);

namespace App\Model\Mcda;

use App\Model\ToolInstance;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
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

    public function critera(): array
    {
        return $this->criteria;
    }

    public function addCriterion(Criterion $criterion): void
    {
        $this->criteria[$criterion->id()] = $criterion->toArray();
    }

    public function updateCriterion(Criterion $criterion): void
    {
        $this->criteria[$criterion->id()] = $criterion->toArray();
    }

    public function removeCriterion(string $id): void
    {
        unset($this->criteria[$id]);
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

    public function setWeightAssignments(array $weightAssignments): void
    {
        $this->weightAssignments = $weightAssignments;
    }

    public function constraints(): array
    {
        return $this->constraints;
    }

    public function setConstraints(array $constraints): void
    {
        $this->constraints = $constraints;
    }

    public function withAhp(): bool
    {
        return $this->withAhp;
    }

    public function setWithAhp(bool $withAhp): void
    {
        $this->withAhp = $withAhp;
    }

    public function suitability(): array
    {
        return $this->suitability;
    }

    public function setSuitability(array $suitability): void
    {
        $this->suitability = $suitability;
    }

    public function data(): array
    {
        return [
            'criteria' => array_values($this->critera()),
            'constraints' => $this->constraints(),
            'suitability' => $this->suitability(),
            'weight_assignments' => $this->weightAssignments(),
            'with_ahp' => $this->withAhp(),
        ];
    }

    public function setData(array $data): void
    {
        $this->criteria = $data['criteria'] ?? $this->criteria;
        $this->constraints = $data['constraints'] ?? $this->constraints;
        $this->suitability = $data['suitability'] ?? $this->suitability;
        $this->weightAssignments = $data['weight_assignments'] ?? $this->weightAssignments;
        $this->withAhp = $data['with_ahp'] ?? $this->withAhp;
    }
}
