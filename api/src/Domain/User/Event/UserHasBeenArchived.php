<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use App\Domain\Common\DomainEvent;
use App\Domain\User\Aggregate\UserAggregate;

final class UserHasBeenArchived extends DomainEvent
{
    /**
     * @param string $aggregateId
     * @return UserHasBeenArchived
     * @throws \Exception
     */
    public static function fromParams(string $aggregateId)
    {
        $self = new self($aggregateId, UserAggregate::NAME, self::getEventNameFromClassname(), []);
        return $self;
    }
}