<?php

declare(strict_types=1);

namespace App\Model\Common;

class Aggregate
{

    public const NAME = '';

    /**
     * @var string
     */
    protected $id;


    public static function withId(string $id): Aggregate
    {
        $self = new self();
        $self->id = $id;
        return $self;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return self::NAME;
    }
}
