<?php

use RetailExpress\SkyLink\Util;

// The format of dates in the V2 API (maybe move this?)
const V2_API_DATE_FORMAT = 'Y-m-d\TH:i:s.000';

/**
 * Returns the value of the given key in an array, providing there is actually a non-empty value there.
 *
 * @param array  $array
 * @param string $key
 * @param mixed  $default
 *
 * @return mixed
 * @codeCoverageIgnore
 */
function array_get_notempty(array $array, $key, $default = null)
{
    return Util::arrayGetNotempty($array, $key, $default);
}
