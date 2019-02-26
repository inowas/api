<?php

namespace App\Model\Modflow;

class Layer
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
        $this->data['id'] = $this->id;
        return $this->data;
    }

    /**
     * @param string $newId
     * @return Layer
     */
    public function clone(string $newId): self
    {
        $this->id = $newId;
        return self::fromArray($this->toArray());
    }
}
