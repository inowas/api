<?php

declare(strict_types=1);

namespace App\Domain\ToolInstance\Command;

use App\Model\Command;
use App\Model\Modflow\Packages;

class UpdateFlopyPackagesCommand extends Command
{

    private $id;
    private $packages;

    /**
     * @return string|null
     */
    public static function getJsonSchema(): ?string
    {
        return sprintf('%s%s', __DIR__, '/../../../../schema/commands/updateFlopyPackages.json');
    }

    /**
     * @param array $payload
     * @return self
     */
    public static function fromPayload(array $payload): self
    {
        $self = new self();
        $self->id = $payload['id'];
        $self->packages = $payload['packages'];
        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function packages(): Packages
    {
        return Packages::fromArray($this->packages);
    }
}