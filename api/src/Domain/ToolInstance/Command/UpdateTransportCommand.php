<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\Modflow\Transport;

class UpdateTransportCommand extends Command
{
    /** @var string */
    private $id;

    /** @var array */
    private $transport;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/updateTransport.json');
    }

    /**
     * @param array $payload
     * @return self
     * @throws \Exception
     */
    public static function fromPayload(array $payload): self
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->transport = $payload['transport'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function transport(): Transport
    {
        return Transport::fromArray($this->transport);
    }
}
