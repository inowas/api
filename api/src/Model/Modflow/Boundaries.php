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

    public function addBoundary(array $boundary): void
    {
        $this->boundaries[$boundary['id']] = $boundary;
    }

    public function removeBoundary(string $id): void
    {
        unset($this->boundaries[$id]);
    }

    public function findById(string $id): ?array
    {
        return $this->boundaries[$id];
    }

    public function toArray(): array
    {
        return $this->boundaries;
    }
}
