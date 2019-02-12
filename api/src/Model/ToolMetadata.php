<?php

declare(strict_types=1);

namespace App\Model;

class ToolMetadata extends ValueObject
{

    private $name;
    private $description;
    private $isPublic;

    public static function fromParams(string $name, string $description, bool $isPublic): ToolMetadata
    {
        $self = new self();
        $self->name = $name;
        $self->description = $description;
        $self->isPublic = $isPublic;
        return $self;
    }

    public static function fromArray(array $arr): self
    {
        $self = new self();
        $self->name = $arr['name'];
        $self->description = $arr['description'];
        $self->isPublic = $arr['public'];
        return $self;
    }

    private function __construct()
    {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'public' => $this->isPublic,
        ];
    }
}
