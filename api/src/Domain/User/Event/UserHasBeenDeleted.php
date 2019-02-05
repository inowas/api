<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use App\Entity\Event;
use App\Domain\User\Aggregate\UserAggregate;

final class UserHasBeenDeleted extends Event
{
    /**
     * @param string $aggregateId
     * @return UserHasBeenDeleted
     * @throws \Exception
     */
    public static function fromParams(string $aggregateId)
    {
        $self = new self($aggregateId, UserAggregate::NAME, self::getEventNameFromClassname(), []);
        return $self;
    }
}
