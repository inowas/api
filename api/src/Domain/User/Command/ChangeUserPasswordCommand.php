<?php

declare(strict_types=1);

namespace App\Domain\User\Command;

use App\Domain\Common\Command;

class ChangeUserPasswordCommand extends Command
{
    private $userId;
    private $password;

    public static function fromPayload(array $payload)
    {
        $self = new self();
        $self->userId = $payload['user_id'] ?? null;
        $self->password = $payload['password'];
        return $self;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function userId(): ?string
    {
        return $this->userId;
    }
}
