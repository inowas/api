<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\Modflow\Transport;

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
     * @return self
     */
    public static function fromPayload(array $payload): self
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

    public function mt3dms(): Transport
    {
        return Transport::fromArray($this->mt3dms);
    }
}
