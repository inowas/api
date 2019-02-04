<?php

declare(strict_types=1);

namespace App\Model\User\Aggregate;

use App\Model\Common\Aggregate;
use App\Model\User\Event\UserHasBeenArchived;
use App\Model\User\Event\UserHasBeenCreated;
use App\Model\User\Event\UserHasBeenDeleted;
use App\Model\User\Event\UserHasBeenReactivated;
use App\Model\User\Event\UsernameHasBeenChanged;
use App\Model\User\Event\UserPasswordHasBeenChanged;
use App\Model\User\Event\UserProfileHasBeenChanged;

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
