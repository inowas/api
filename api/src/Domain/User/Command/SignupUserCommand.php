<?php

declare(strict_types=1);

namespace App\Domain\User\Command;

use App\Model\Command;

class SignupUserCommand extends Command
{
    private $name;
    private $email;
    private $password;

    public static function fromParams(string $name, string $email, string $password): self
    {
        $self = new self();
        $self->name = $name;
        $self->email = $email;
        $self->password = $password;
        return $self;
    }

    public static function fromPayload(array $payload): self
    {
        $self = new self();
        $self->name = $payload['name'] ?? null;
        $self->email = $payload['email'] ?? null;
        $self->password = $payload['password'] ?? null;
        return $self;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function email(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function password(): string
    {
        return $this->password;
    }
}
