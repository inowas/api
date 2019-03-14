<?php

namespace App\Model\Modflow;

use App\Model\ValueObject;

final class Calculation extends ValueObject
{
    private $id;

    public static function fromArray(array $arr): self
    {
        $self = new self();
        $self->id = $arr['id'] ?? null;
        return $self;
    }

    private function __construct()
    {
    }

    public function toArray(): array
    {
        return [
            "id" => $this->id
        ];
    }
}
