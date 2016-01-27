<?php

namespace RetailExpress\SkyLink\Customers;

use RetailExpress\SkyLink\ValueObject;
use InvalidArgumentException;

abstract class Address implements ValueObject
{
    private static $phoneTypes = ['phone', 'mobile', 'fax'];

    private $lines = [];

    private $suburb;

    private $postcode;

    private $state;

    private $country;

    private $phones = [];

    private $company;

    public function __construct(
        $lines,
        $suburb,
        $postcode,
        $state,
        $country = null,
        array $phones = [],
        Company $company = null
    )
    {
        $lines = (array) $lines;
        $this->validateLines($lines);
        $this->lines = $lines;

        $this->validatePhones($phones);
        $this->phones = $phones;

        $this->company = $company;
    }

    public static function forIndividual(
        $lines,
        $suburb,
        $postcode,
        $state,
        $country = null,
        array $phones = []
    )
    {
        return new self($lines, $suburb, $postcode, $state, $country, $phones);
    }

    public static function forCompany(
        Company $company,
        $lines,
        $suburb,
        $postcode,
        $state,
        $country = null,
        array $phones = []
    )
    {
        return new self($lines, $suburb, $postcode, $state, $country, $phones, $company);
    }

    public function equals(ValueObject $other)
    {
        return $other->number === $this->number;
    }

    private function validateLines(array $lines)
    {
        $lines = array_map(function ($line) {
            return (string) $line;
        }, $lines);

        if (!isset($lines[0])) {
            throw new InvalidArgumentException("At least one address line is required to create an address.");
        }

        // There is a maximum of two lines
        return array_slice($lines, 0, 2);
    }

    private function validatePhones(array $phones)
    {
        foreach (array_keys($phones) as $type) {
            if (!in_array($type, self::$phoneTypes)) {
                throw new InvalidArgumentException("Phone type \"{$type}\" is not valid.");
            }
        }
    }
}
