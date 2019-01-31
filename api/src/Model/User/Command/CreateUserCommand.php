<?php

declare(strict_types=1);

namespace App\Model\User\Command;

use App\Model\Common\Command;

class CreateUserCommand extends Command
{
    private $username;
    private $password;
    private $roles;
    private $enabled;

    public static function fromParams(string $username, string $password, array $roles = [], bool $enabled = true)
    {
        $self = new self();
        $self->username = $username;
        $self->password = $password;
        $self->roles = $roles;
        $self->enabled = $enabled;
        return $self;
    }

    public static function fromPayload(array $payload)
    {
        $self = new self();
        $self->username = $payload['username'] ?? null;
        $self->password = $payload['password'] ?? null;
        $self->roles = $payload['roles'] ?? ['ROLE_USER'];
        $self->enabled = $payload['enables'] ?? true;
        return $self;
    }

    /**
     * @return mixed
     */
    public function username(): string
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function password(): string
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function roles(): array
    {
        return $this->roles;
    }

    /**
     * @return mixed
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
