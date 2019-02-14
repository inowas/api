<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\Modflow\Boundary;

class AddBoundaryCommand extends Command
{

    private $id;
    private $boundary;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/addBoundary.json');
    }

    /**
     * @param array $payload
     * @return self
     */
    public static function fromPayload(array $payload): self
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->boundary = $payload['boundary'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function boundary(): Boundary
    {
        return Boundary::fromArray($this->boundary);
    }
}
