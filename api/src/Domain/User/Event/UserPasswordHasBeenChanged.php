<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use App\Domain\Common\DomainEvent;
use App\Domain\User\Aggregate\UserAggregate;

final class UserPasswordHasBeenChanged extends DomainEvent
{
    private $password;

    /**
     * @param string $aggregateId
     * @param string $password
     * @return UserPasswordHasBeenChanged
     * @throws \Exception
     */
    public static function fromParams(string $aggregateId, string $password)
    {
        $self = new self($aggregateId, UserAggregate::NAME, self::getEventNameFromClassname(), [
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
