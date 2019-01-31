<?php

declare(strict_types=1);

namespace App\Model\User\Command;

use App\Model\Common\Command;

class ReactivateUserCommand extends Command
{
    private $userId;

    public static function fromPayload(array $payload)
    {
        $self = new self();
        $self->userId = $payload['user_id'] ?? null;
        return $self;
    }

    public function userId(): ?string
    {
        return $this->userId;
    }
}
