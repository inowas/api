<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use Exception;

class DeleteToolInstanceCommand extends Command
{

    /** @var string */
    private $id;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/deleteToolInstance.json');
    }

    /**
     * @param string $id
     * @return DeleteToolInstanceCommand
     * @throws Exception
     */
    public static function fromParams(string $id): DeleteToolInstanceCommand
    {
        $self = new self();
        $self->id = $id;
        return $self;
    }

    /**
     * @param array $payload
     * @return DeleteToolInstanceCommand
     * @throws Exception
     */
    public static function fromPayload(array $payload): DeleteToolInstanceCommand
    {
        $self = new self();
        $self->id = $payload['id'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }
}
