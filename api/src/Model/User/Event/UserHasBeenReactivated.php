<?php

declare(strict_types=1);

namespace App\Model\User\Event;

use App\Entity\Event;
use App\Model\User\Aggregate\UserAggregate;

final class UserHasBeenReactivated extends Event
{
    /**
     * @param string $aggregateId
     * @return UserHasBeenReactivated
     * @throws \Exception
     */
    public static function fromParams(string $aggregateId)
    {
        $self = new self($aggregateId, UserAggregate::NAME, self::getEventNameFromClassname(), []);
        return $self;
    }
}
