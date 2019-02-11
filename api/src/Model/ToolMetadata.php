<?php

declare(strict_types=1);

namespace App\Model;

class ToolMetadata
{

    private $name;
    private $description;
    private $isPublic;

    public static function fromParams(string $name, string $description, bool $isPublic): ToolMetadata
    {
        return new self($name, $description, $isPublic);
    }

    public static function fromArray(array $arr): ToolMetadata
    {
        $name = $arr['name'];
        $description = $arr['description'];
        $isPublic = $arr['public'];
        return new self($name, $description, $isPublic);
    }

    private function __construct(string $name, string $description, bool $isPublic)
    {
        $this->name = $name;
        $this->description = $description;
        $this->isPublic = $isPublic;
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
