<?php

namespace App\Support;

use Illuminate\Support\Arr as BaseArr;

class Arr extends BaseArr
{
    public function iexplode($delimiters, $string, $limit = PHP_INT_MAX)
    {
        if (! is_array($delimiters)) {
            $delimiters = [$delimiters];
        }
        $delimiter = reset($delimiters);

        $result = [];
        $maskedString = $string;
        while (count($result) + 1 < $limit) {
            $c = 1;
            $maskedStringExploded = str_replace($delimiters, $delimiter, $maskedString, $c);
            $maskedStringExploded = explode($delimiter, $maskedStringExploded, 2);
            if (count($maskedStringExploded) == 2) {
                $result[] = $maskedStringExploded[0];
                $maskedString = $maskedStringExploded[1];
            } else {
                $maskedString = $maskedStringExploded[0];
                break;
            }
        }
        $result[] = $maskedString;

        return $result;
    }

    public function filterArray($array, $doFilter = true, $checkUnique = true, $doTrim = true): array
    {
        if ($doTrim) {
            $array = array_map('trim', $array);
        }
        if ($checkUnique) {
            $array = array_unique($array);
        }
        if ($doFilter) {
            $array = array_filter($array, 'strlen');
        }

        return $array;
    }

    public function templatedArray($template = [], $values = [], $const = [])
    {
        return $const + array_intersect_key($values, $template) + $template;
    }

    public function trimRecursive($array)
    {
        array_walk_recursive(
            $array,
            function (&$item) {
                $item = ltrim($item);
            }
        );

        return $array;
    }
}
