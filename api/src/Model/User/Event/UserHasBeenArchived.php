<?php

declare(strict_types=1);

namespace App\Model\User\Event;

use App\Entity\Event;
use App\Model\User\Aggregate\UserAggregate;

final class UserHasBeenArchived extends Event
{
    public const NAME = 'userHasBeenArchived';
    public const AGGREGATE_NAME = UserAggregate::NAME;

    /**
     * @param string $aggregateId
     * @return UserHasBeenArchived
     * @throws \Exception
     */
    public static function fromParams(string $aggregateId)
    {
        $self = new self($aggregateId, self::AGGREGATE_NAME, self::NAME, []);
        return $self;
    }
}
