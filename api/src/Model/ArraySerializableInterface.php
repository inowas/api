<?php

namespace App\Model;


interface ArraySerializableInterface
{
    public static function fromArray(array $arr);

    public function toArray(): array;
}
