<?php

namespace App\Model\Mcda;

class Criterion
{
    /** @var string */
    protected $id;

    protected $data;

    public static function fromArray(array $arr): self
    {
        $self = new self();
        $self->id = $arr['id'];
        $self->data = $arr;
        return $self;
    }

    private function __construct()
    {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
