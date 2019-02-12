<?php

namespace App\Model;


abstract class ValueObject implements ArraySerializableInterface
{
    public function isEqualTo($toolMetadata): bool
    {
        if (!method_exists($toolMetadata, 'toArray')) {
            return false;
        }

        return $toolMetadata->toArray() == $this->toArray();
    }

    public function diff($newData): ?array
    {
        if (!method_exists($newData, 'toArray')) {
            return null;
        }

        return $this->array_recursive_diff($newData->toArray(), $this->toArray());
    }

    public function merge(array $diff): self
    {
        $arr = $this->toArray();
        return static::fromArray($this->array_merge_recursive_diff($arr, $diff));
    }

    protected function array_recursive_diff($aArray1, $aArray2)
    {
        $aReturn = array();

        foreach ($aArray1 as $mKey => $mValue) {
            if (array_key_exists($mKey, $aArray2)) {
                if (is_array($mValue)) {
                    $aRecursiveDiff = $this->array_recursive_diff($mValue, $aArray2[$mKey]);
                    if (count($aRecursiveDiff)) {
                        $aReturn[$mKey] = $aRecursiveDiff;
                    }
                } else {
                    if ($mValue != $aArray2[$mKey]) {
                        $aReturn[$mKey] = $mValue;
                    }
                }
            } else {
                $aReturn[$mKey] = $mValue;
            }
        }
        return $aReturn;
    }

    protected function array_merge_recursive_diff(array &$array1, &$array2 = null): array
    {
        $merged = $array1;

        if (is_array($array2)) {
            foreach ($array2 as $key => $val) {
                if (is_array($array2[$key])) {
                    $merged[$key] = is_array($merged[$key]) ? $this->array_merge_recursive_diff($merged[$key], $array2[$key]) : $array2[$key];
                } else {
                    $merged[$key] = $val;
                }
            }
        }

        return $merged;
    }
}
