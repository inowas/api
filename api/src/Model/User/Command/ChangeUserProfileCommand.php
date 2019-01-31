<?php

declare(strict_types=1);

namespace App\Model\User\Command;

use App\Model\Common\Command;

class ChangeUserProfileCommand extends Command
{
    private $userId;
    private $profile;

    public static function fromPayload(array $payload)
    {
        $self = new self();
        $self->userId = $payload['user_id'] ?? null;
        $self->profile = $payload['profile'] ?? [];
        return $self;
    }

    public function profile(): array
    {
        return $this->profile;
    }

    public function userId(): ?string
    {
        return $this->userId;
    }
}
