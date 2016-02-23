<?php

namespace RetailExpress\SkyLink\Outlets;

use RetailExpress\SkyLink\Address as BaseAddress;
use RetailExpress\SkyLink\ValueObject;

class Address extends BaseAddress implements ValueObject
{
    public static function newInstance(
        $lines = [],
        $suburb = null,
        $postcode = null,
        $state = null,
        $country = null,
        array $phones = []
    ) {
        return new self($lines, $suburb, $postcode, $state, $country, $phones);
    }
}
