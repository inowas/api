<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\Command;

use App\Model\Command;

class UpdateMt3dmsCommand extends Command
{

    private $id;
    private $mt3dms;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/updateMt3dms.json');
    }

    /**
     * @param array $payload
     * @return UpdateMt3dmsCommand
     */
    public static function fromPayload(array $payload): UpdateMt3dmsCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->mt3dms = $payload['mt3dms'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function mt3dms(): array
    {
        return $this->mt3dms;
    }
}
