<?php

namespace App\Model\Modflow;

use App\Model\ValueObject;

final class Calculation extends ValueObject
{
    private $calculationId;

    public static function fromArray(array $arr): self
    {
        $self = new self();
        $self->calculationId = $arr['calculation_id'];
        return $self;
    }

    private function __construct()
    {
    }

    public function toArray(): array
    {
        return [
            "calculation_id" => $this->calculationId
        ];
    }
}
