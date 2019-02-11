<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;

abstract class Projector
{

    abstract public function aggregateName(): string;

    abstract protected function truncateTable(): void;

    public function apply(DomainEvent $e): void
    {
        $this->onEvent($e);
    }

    public function recreateFromHistory(ArrayCollection $events): void
    {
        $this->truncateTable();
        foreach ($events->getIterator() as $event) {
            $this->onEvent($event);
        }
    }

    protected function onEvent(DomainEvent $e): void
    {
        $handler = $this->determineEventMethodFor($e);
        if (!method_exists($this, $handler)) {
            throw new \RuntimeException(sprintf(
                'Missing event method %s for projector %s', $handler, \get_class($this)
            ));
        }
        $this->{$handler}($e);
    }

    protected function determineEventMethodFor(DomainEvent $e): string
    {
        return 'on' . implode(\array_slice(explode('\\', \get_class($e)), -1));
    }

    /**
     * @param array $array1
     * @param null $array2
     * @return array
     */
    protected function array_merge_recursive_distinct(array $array1, $array2 = null): array
    {
        function &array_merge_recursive_distinct(array &$array1, &$array2 = null)
        {
            $merged = $array1;

            if (is_array($array2)) {
                foreach ($array2 as $key => $val) {
                    if (is_array($array2[$key]))
                        $merged[$key] = is_array($merged[$key]) ? array_merge_recursive_distinct($merged[$key], $array2[$key]) : $array2[$key];
                    else
                        $merged[$key] = $val;
                }
            }

            return $merged;
        }

        ;

        return array_merge_recursive_distinct($array1, $array2);
    }

    /**
     * @param array $array1
     * @param array $array2
     * @return array
     * $array1 = ['boundaries' => [['id' => 1], ['id' => 2],['id' => 3]]];
     * $array2 = ['boundaries' => ['id' => 1]];
     * $result = ['boundaries' => [['id' => 2],['id' => 3]]];
     */
    protected function array_remove_element(array $array1, array $array2)
    {
        function recursiveRemoval(&$array1, &$array2)
        {
            $final = $array1;
            if (is_array($array2)) {
                foreach ($array2 as $key => $val) {
                    if (is_array($val)) {
                        $final[$key] = recursiveRemoval($final[$key], $val);
                    } else {
                        foreach ($final as $k => &$v) {
                            if ($v[$key] === $val) {
                                unset($final[$k]);
                                $final = array_values($final);
                            }
                        }
                    }
                }
            }
            return $final;
        }


        return recursiveRemoval($array1, $array2);
    }
}
