<?php

declare(strict_types=1);

namespace App\Model\ScenarioAnalysis;


use Ramsey\Uuid\Uuid;

class ScenarioAnalysis
{
    private $baseId;
    private $scenarioIds = [];

    public static function createWithBaseId($baseId): ScenarioAnalysis
    {
        $self = new self();
        $self->baseId = $baseId;
        return $self;
    }

    public static function fromArray($arr): ScenarioAnalysis
    {
        $self = new self();
        $self->baseId = $arr['base_id'];
        $self->scenarioIds = $arr['scenario_ids'] ?? [];
        return $self;
    }

    /**
     * @throws \Exception
     */
    public function __clone()
    {
        $this->baseId = Uuid::uuid4()->toString();
        foreach ($this->scenarioIds as $key => $scenarioId) {
            $this->scenarioIds[$key] = Uuid::uuid4()->toString();
        }
    }

    public function baseId(): string
    {
        return $this->baseId;
    }

    public function scenarioIds(): array
    {
        return $this->scenarioIds;
    }

    public function addScenarioId($scenarioId)
    {
        $this->scenarioIds[] = $scenarioId;
    }

    public function removeScenarioId($scenarioIdToRemove)
    {
        foreach ($this->scenarioIds as $key => $scenarioId) {
            if ($scenarioId === $scenarioIdToRemove) {
                unset($this->scenarioIds[$key]);
            }
        }

        $this->scenarioIds = array_values($this->scenarioIds);
    }

    public function toArray()
    {
        return [
            'base_id' => $this->baseId,
            'scenario_ids' => $this->scenarioIds,
        ];
    }
}
