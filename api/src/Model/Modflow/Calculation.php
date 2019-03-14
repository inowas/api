<?php

namespace App\Model\Modflow;

use App\Model\ValueObject;

final class Calculation extends ValueObject
{
    private $latest;
    private $history = [];

    public static function fromArray(array $arr): self
    {
        $self = new self();
        $self->latest = $arr['latest'] ?? null;
        return $self;
    }

    private function __construct()
    {
    }

    public function latest(): ?string
    {
        return $this->latest;
    }

    public function addCalculationId(string $calculationId): void
    {

        if ($this->latest === $calculationId) {
            return;
        }

        $this->history[] = $this->latest;
        $this->latest = $calculationId;
    }

    /**
     * @return array
     */
    public function getHistory(): array
    {
        return $this->history;
    }

    /**
     * @param array $history
     */
    public function setHistory(array $history): void
    {
        $this->history = $history;
    }


    public function toArray(): array
    {
        return array(
            'latest' => $this->latest,
            'history' => $this->history,
        );
    }
}
