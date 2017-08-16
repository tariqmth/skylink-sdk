<?php

use RetailExpress\SkyLink\Sdk\Util;

// The format of dates in the V2 API (maybe move this?)
const V2_API_DATE_FORMAT = 'Y-m-d\TH:i:s.000';
const V2_API_TIMEZONE = 'Australia/Sydney';

/**
 * Takes a Date Time object and converts it to a formatted Retail Express date, in Sydne time.
 *
 * @return string
 */
function to_v2_rex_date(DateTimeImmutable $date)
{
    return $date->setTimezone(new DateTimeZone(V2_API_TIMEZONE))->format(V2_API_DATE_FORMAT);
}

/**
 * Takes a date from Retail Express' V2 API (which is usually, but not always, in the format
 * above), which is always in Sydney time, and converts it to a UTC date.
 *
 * @param string $date
 *
 * @return DateTimeImmutable
 */
function from_v2_rex_date($date)
{
    return new DateTimeImmutable($date, new DateTimeZone(V2_API_TIMEZONE));
}


/**
 * Takes a date from Retail Express, converts it to UTC and provides a timestamp.
 *
 * @param string $date
 *
 * @return int $timestamp
 */
function from_v2_rex_date_to_timestamp($date)
{
    return from_v2_rex_date($date)->getTimestamp();
}

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
