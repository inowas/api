<?php

declare(strict_types=1);

namespace App\Model\User\Event;

use App\Entity\Event;
use App\Model\User\Aggregate\UserAggregate;

final class UsernameHasBeenChanged extends Event
{
    private $username;

    public const AGGREGATE_NAME = UserAggregate::NAME;

    /**
     * @param string $aggregateId
     * @param string $username
     * @return UsernameHasBeenChanged
     * @throws \Exception
     */
    public static function fromParams(string $aggregateId, string $username)
    {
        $self = new self($aggregateId, self::AGGREGATE_NAME, self::eventName(), [
            'username' => $username,
        ]);

        $self->username = $username;
        return $self;
    }

    public function username(): ?string
    {
        if (!$this->username) {
            $this->username = $this->payload['username'];
        }

        return $this->username;
    }
}
