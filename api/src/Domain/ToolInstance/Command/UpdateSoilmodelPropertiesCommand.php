<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use Exception;

class UpdateSoilmodelPropertiesCommand extends Command
{
    /** @var string */
    private $id;

    /** @var array */
    private $properties;

    /**
     * @return string
     */
    public static function getJsonSchema(): string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/updateSoilmodelProperties.json');
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
        $self->properties = $payload['properties'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function properties(): array
    {
        return $this->properties;
    }
}
