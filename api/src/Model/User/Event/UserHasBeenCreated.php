<?php

declare(strict_types=1);

namespace App\Model\User\Event;

use App\Entity\Event;
use App\Model\User\Aggregate\UserAggregate;

final class UserHasBeenCreated extends Event
{

    private $username;
    private $password;
    private $roles;
    private $enabled;

    public const NAME = 'userHasBeenCreated';
    public const AGGREGATE_NAME = UserAggregate::NAME;

    /**
     * @param string $aggregateId
     * @param string $username
     * @param string $password
     * @param array $roles
     * @param bool $enabled
     * @return UserHasBeenCreated
     * @throws \Exception
     */
    public static function fromParams(string $aggregateId, string $username, string $password, array $roles = [], bool $enabled = true)
    {
        $self = new self($aggregateId, UserAggregate::NAME, self::NAME, [
            'username' => $username,
            'password' => $password,
            'roles' => $roles,
            'enabled' => $enabled
        ]);

        $self->username = $username;
        $self->password = $password;
        $self->roles = $roles;
        $self->enabled = $enabled;
        return $self;
    }

    public function username(): string
    {
        if (!$this->username) {
            $this->username = $this->payload()['username'];
        }

        return $this->username;
    }

    public function password(): string
    {
        if (!$this->password) {
            $this->password = $this->payload()['password'];
        }

        return $this->password;
    }

    public function roles(): array
    {
        if (!$this->roles) {
            $this->roles = $this->payload()['roles'];
        }

        return $this->roles;
    }

    public function isEnabled(): bool
    {
        if (!$this->enabled) {
            $this->enabled = $this->payload()['enabled'];
        }

        return $this->enabled;
    }
}
