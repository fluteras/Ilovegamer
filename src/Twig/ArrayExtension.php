<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ArrayExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('values', [$this, 'twig_get_array_values_filter']),
        ];
    }
    function twig_get_array_values_filter($array)
    {        
        if ($array instanceof \Traversable) {
            while ($array instanceof \IteratorAggregate) {
                $array = $array->getIterator();
            }

            $values = [];
            
            if ($array instanceof \Iterator) {
                $array->rewind();
                while ($array->valid()) {
                    $values[] = $array->current();
                    $array->next();
                }

                return $values;
            }
            
            foreach ($array as $key => $item) {
                $values[] = $item;
            }

            return $values;
        }

        if (!\is_array($array)) {
            return [];
        }

        return array_values($array);
    }
}



