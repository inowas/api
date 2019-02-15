<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;

class McdaUpdateProjectCommand extends Command
{

    private $id;
    private $data;

    /**
     * @return string
     */
    public static function getJsonSchema(): string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/mcdaUpdateProject.json');
    }

    /**
     * @param array $payload
     * @return self
     */
    public static function fromPayload(array $payload): self
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->data = $payload['data'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function data(): array
    {
        return $this->data;
    }
}
