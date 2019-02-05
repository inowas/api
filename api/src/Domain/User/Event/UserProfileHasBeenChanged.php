<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use App\Entity\Event;
use App\Domain\User\Aggregate\UserAggregate;

final class UserProfileHasBeenChanged extends Event
{
    private $profile;

    /**
     * @param string $aggregateId
     * @param array $profile
     * @return UserProfileHasBeenChanged
     * @throws \Exception
     */
    public static function fromParams(string $aggregateId, array $profile)
    {
        $self = new self($aggregateId, UserAggregate::NAME, self::getEventNameFromClassname(), [
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
