<?php

declare(strict_types=1);

namespace App\Domain\Common;

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
}
