<?php

declare(strict_types=1);

namespace App\Domain\ModflowModel\Command;

use App\Model\Command;

class RemoveBoundaryCommand extends Command
{

    private $id;
    private $boundaryId;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/removeBoundary.json');
    }

    /**
     * @param array $payload
     * @return RemoveBoundaryCommand
     */
    public static function fromPayload(array $payload): RemoveBoundaryCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->boundaryId = $payload['boundary_id'];
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
}
