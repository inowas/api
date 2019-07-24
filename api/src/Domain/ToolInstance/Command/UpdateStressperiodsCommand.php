<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use Exception;

class UpdateStressperiodsCommand extends Command
{
    /** @var string */
    private $id;

    /** @var array */
    private $stressperiods;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/updateStressperiods.json');
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
        $self->stressperiods = $payload['stressperiods'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function stressperiods(): array
    {
        return $this->stressperiods;
    }
}
