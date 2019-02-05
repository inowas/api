<?php

declare(strict_types=1);

namespace App\Domain\User\Command;

use App\Domain\Common\Command;

class DeleteUserCommand extends Command
{
    private $userId;

    public static function fromPayload(array $payload)
    {
        $self = new self();
        $self->userId = $payload['user_id'];
        return $self;
    }

    public function userId(): string
    {
        return $this->userId;
    }
}
