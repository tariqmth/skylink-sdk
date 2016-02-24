<?php

/**
 * Returns the value of the given key in an array, providing there is actually a non-empty value there.
 *
 * @param array  $array
 * @param string $key
 * @param mixed  $default
 *
 * @return mixed
 */
function array_get_notempty($array, $key, $default = null)
{
    $flattened = array_dot($array);

    if (isset($flattened[$key]) && !empty($flattened[$key])) {
        return $flattened[$key];
    }

    return value($default);
}
