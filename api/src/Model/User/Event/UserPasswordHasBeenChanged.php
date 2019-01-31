<?php

declare(strict_types=1);

namespace App\Model\User\Event;

use App\Entity\Event;
use App\Model\User\Aggregate\UserAggregate;

final class UserPasswordHasBeenChanged extends Event
{
    private $password;

    public const AGGREGATE_NAME = UserAggregate::NAME;

    /**
     * @param string $aggregateId
     * @param string $password
     * @return UserPasswordHasBeenChanged
     * @throws \Exception
     */
    public static function fromParams(string $aggregateId, string $password)
    {
        $self = new self($aggregateId, self::AGGREGATE_NAME, self::eventName(), [
            'password' => $password,
        ]);

        $self->password = $password;
        return $self;
    }

    public function password(): string
    {
        if (!$this->password) {
            $this->password = $this->payload['password'];
        }

        return $this->password;
    }
}
