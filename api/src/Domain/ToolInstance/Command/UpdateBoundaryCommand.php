<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\Modflow\Boundary;

class UpdateBoundaryCommand extends Command
{

    private $id;
    private $boundaryId;
    private $boundary;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/updateBoundary.json');
    }

    /**
     * @param array $payload
     * @return self
     */
    public static function fromPayload(array $payload): self
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->boundaryId = $payload['boundary_id'];
        $self->boundary = $payload['boundary'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function boundaryId(): string
    {
        return $this->boundaryId;
    }

    public function boundary(): Boundary
    {
        return Boundary::fromArray($this->boundary);
    }
}
