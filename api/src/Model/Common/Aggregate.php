<?php

declare(strict_types=1);

namespace App\Model\Common;

use Ramsey\Uuid\Uuid;

class Aggregate
{
    /**
     * @var Uuid
     */
    private $id;

    public static function fromId(Uuid $id)
    {
        $self = new self();
        $self->id = $id;
        return $self;
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }
}
