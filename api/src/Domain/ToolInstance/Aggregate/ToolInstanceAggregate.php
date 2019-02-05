<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Aggregate;

use App\Domain\Common\Aggregate;
use App\Domain\ToolInstance\Event\ToolInstanceHasBeenCreated;

class ToolInstanceAggregate extends Aggregate
{
    public const NAME = 'toolInstance';

    public static $registeredEvents = [
        ToolInstanceHasBeenCreated::class
    ];
}
