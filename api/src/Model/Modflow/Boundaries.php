<?php

namespace App\Model\Modflow;

use App\Model\ValueObject;

final class Boundaries extends ValueObject
{
    private $boundaries;

    public static function fromArray(array $arr): self
    {
        $self = new self();
        $self->boundaries = $arr;
        return $self;
    }

    public static function create(): self
    {
        return new self();
    }

    private function __construct()
    {
    }

    public function first(): ?Boundary
    {
        if (count($this->boundaries) === 0) {
            return null;
        }

        /** @noinspection PhpParamsInspection */
        return Boundary::fromArray(array_values($this->boundaries)[0]);
    }

    public function addBoundary(Boundary $boundary): void
    {
        $this->updateBoundary($boundary);
    }

    public function updateBoundary(Boundary $boundary): void
    {
        $this->boundaries[$boundary->id()] = $boundary->toArray();
    }

    public function removeBoundary(string $id): void
    {
        unset($this->boundaries[$id]);
    }

    public function findById(string $id): ?Boundary
    {
        if (!array_key_exists($id, $this->boundaries)) {
            return null;
        }

        return Boundary::fromArray($this->boundaries[$id]);
    }

    public function toArray(): array
    {
        return $this->boundaries;
    }
}
