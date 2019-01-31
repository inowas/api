<?php

declare(strict_types=1);

namespace App\Model\User\Event;

use App\Entity\Event;
use App\Model\User\Aggregate\UserAggregate;

final class UserProfileHasBeenChanged extends Event
{
    private $profile;

    public const NAME = 'userProfileHasBeenChanged';
    public const AGGREGATE_NAME = UserAggregate::NAME;

    /**
     * @param string $aggregateId
     * @param array $profile
     * @return UserProfileHasBeenChanged
     * @throws \Exception
     */
    public static function fromParams(string $aggregateId, array $profile)
    {
        $self = new self($aggregateId, self::AGGREGATE_NAME, self::NAME, [
            'profile' => $profile,
        ]);

        $self->profile = $profile;
        return $self;
    }

    public function profile(): array
    {
        if (!$this->profile) {
            $this->profile = $this->payload['profile'];
        }

        return $this->profile;
    }
}
