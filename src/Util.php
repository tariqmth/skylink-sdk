<?php

namespace RetailExpress\SkyLink\Sdk;

use Illuminate\Support\Arr;

class Util
{
    /**
     * Returns the value of the given key in an array, providing there is actually a non-empty value there.
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function arrayGetNotempty(array $array, $key, $default = null)
    {
        $flattened = Arr::dot($array);

        if (isset($flattened[$key]) && !empty($flattened[$key])) {
            return $flattened[$key];
        }

        return value($default);
    }
}
