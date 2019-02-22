<?php

declare(strict_types=1);

namespace App\Model\Modflow\Boundary;

final class BoundaryCollection
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

    /**
     * @return BoundaryInterface|null
     * @throws \Exception
     */
    public function first(): ?BoundaryInterface
    {
        if (count($this->boundaries) === 0) {
            return null;
        }

        /** @noinspection PhpParamsInspection */
        return BoundaryFactory::fromArray(array_values($this->boundaries)[0]);
    }

    public function addBoundary(BoundaryInterface $boundary): void
    {
        $this->updateBoundary($boundary);
    }

    public function updateBoundary(BoundaryInterface $boundary): void
    {
        $this->boundaries[$boundary->id()] = $boundary->toArray();
    }

    public function removeBoundary(string $id): void
    {
        unset($this->boundaries[$id]);
    }

    /**
     * @param string $id
     * @return BoundaryInterface|null
     * @throws \Exception
     */
    public function findById(string $id): ?BoundaryInterface
    {
        if (!array_key_exists($id, $this->boundaries)) {
            return null;
        }

        return BoundaryFactory::fromArray($this->boundaries[$id]);
    }

    public function toArray(): array
    {
        return $this->boundaries;
    }
}
