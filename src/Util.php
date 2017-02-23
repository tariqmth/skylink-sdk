<?php

namespace RetailExpress\SkyLink\Sdk;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
        // Assign a random default value to compare against
        $randomDefault = Str::random();
        $value = Arr::get($array, $key, $randomDefault);

        // If the parent function returned nothing, we'll just return our given default
        if ($value === $randomDefault) {
            return value($default);
        }

        // If the returned value is empty, we'll return our given default
        if (empty($value)) {
            return value($default);
        }

        return $value;
    }
}
