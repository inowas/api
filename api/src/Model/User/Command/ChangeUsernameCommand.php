<?php

declare(strict_types=1);

namespace App\Model\User\Command;

use App\Model\Common\Command;

class ChangeUsernameCommand extends Command
{
    private $userId;
    private $username;

    public static function fromPayload(array $payload)
    {
        $self = new self();
        $self->userId = $payload['user_id'] ?? null;
        $self->username = $payload['username'];
        return $self;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function userId(): ?string
    {
        return $this->userId;
    }
}
