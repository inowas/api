<?php

declare(strict_types=1);

namespace App\Model;

final class ProjectorCollection
{
    private $projectors = [];

    public function __construct(iterable $projectors)
    {
        foreach ($projectors as $projector) {
            $this->projectors[] = $projector;
        }
    }

    public function toArray(): array
    {
        return $this->projectors;
    }

    public function getProjector($class): ?Projector
    {
        foreach ($this->projectors as $projector) {
            if (get_class($projector) === $class) {
                return $projector;
            }
        }

        return null;
    }
}
