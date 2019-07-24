<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\Modflow\Boundary\BoundaryFactory;
use App\Model\Modflow\Boundary\BoundaryInterface;
use Exception;

class UpdateBoundaryCommand extends Command
{
    /** @var string */
    private $id;

    /** @var array */
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
     * @throws Exception
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

    /**
     * @return BoundaryInterface
     * @throws Exception
     */
    public function boundary(): BoundaryInterface
    {
        return BoundaryFactory::fromArray($this->boundary);
    }
}
