<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Aggregate;

use App\Model\Aggregate;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCloned;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCreated;

class ToolInstanceAggregate extends Aggregate
{
    public const NAME = 'toolInstance';

    public static $registeredEvents = [
        ToolInstanceHasBeenCreated::class,
        ToolInstanceHasBeenCloned::class
    ];
}
