<?php

declare(strict_types=1);

namespace App\Model;

final class Diff
{
    private $classname;
    private $data;

    public static function fromClassnameWithData(string $classname, array $data): self
    {
        return new self($classname, $data);
    }

    public static function fromArray(array $arr): self
    {
        return new self($arr['classname'], $arr['data']);
    }

    private function __construct(string $classname, array $data)
    {
        $this->classname = $classname;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function classname(): string
    {
        return $this->classname;
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return [
            'classname' => $this->classname,
            'data' => $this->data
        ];
    }
}
