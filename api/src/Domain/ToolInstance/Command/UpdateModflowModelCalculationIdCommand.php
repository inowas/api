<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use Exception;

class UpdateModflowModelCalculationIdCommand extends Command
{

    /** @var string */
    private $id;

    /** @var string */
    private $calculationId;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/updateModflowModelCalculationId.json');
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
        $self->calculationId = $payload['calculation_id'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function calculationId(): string
    {
        return $this->calculationId;
    }
}
