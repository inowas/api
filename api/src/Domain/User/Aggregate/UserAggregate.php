<?php

declare(strict_types=1);

namespace App\Domain\User\Aggregate;

use App\Domain\Common\Aggregate;
use App\Domain\User\Event\UserHasBeenArchived;
use App\Domain\User\Event\UserHasBeenCreated;
use App\Domain\User\Event\UserHasBeenDeleted;
use App\Domain\User\Event\UserHasBeenReactivated;
use App\Domain\User\Event\UsernameHasBeenChanged;
use App\Domain\User\Event\UserPasswordHasBeenChanged;
use App\Domain\User\Event\UserProfileHasBeenChanged;

class UserAggregate extends Aggregate
{
    public const NAME = 'user';

    public static $registeredEvents = [
        UserHasBeenArchived::class,
        UserHasBeenCreated::class,
        UserHasBeenDeleted::class,
        UserHasBeenReactivated::class,
        UsernameHasBeenChanged::class,
        UserPasswordHasBeenChanged::class,
        UserProfileHasBeenChanged::class
    ];
}
