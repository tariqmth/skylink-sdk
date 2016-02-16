<?php

namespace RetailExpress\SkyLink\Customers;

use RetailExpress\SkyLink\ValueObject;
use InvalidArgumentException;

abstract class Address implements ValueObject
{
    private static $phoneTypes = ['phone', 'mobile', 'fax'];

    private $firstName;

    private $lastName;

    private $lines = [];

    private $suburb;

    private $postcode;

    private $state;

    private $country;

    private $phones = [];

    private $company;

    public function __construct(
        $firstName,
        $lastName,
        $lines,
        $suburb,
        $postcode,
        $state,
        $country = null,
        array $phones = [],
        Company $company = null
    )
    {
        $this->firstName = (string) $firstName;
        $this->lastName = (string) $lastName;

        $lines = (array) $lines;
        $this->validateLines($lines);
        $this->lines = $lines;

        $this->suburb = $suburb;
        $this->postcode = $postcode;
        $this->state = $state;
        $this->country = $country;

        $this->validatePhones($phones);
        $this->phones = $phones;

        $this->company = $company;
    }

    public static function forIndividual(
        $firstName,
        $lastName,
        $lines,
        $suburb,
        $postcode,
        $state,
        $country = null,
        array $phones = []
    )
    {
        return new static(
            $firstName,
            $lastName,
            $lines,
            $suburb,
            $postcode,
            $state,
            $country,
            $phones
        );
    }

    public static function forCompany(
        Company $company,
        $firstName,
        $lastName,
        $lines,
        $suburb,
        $postcode,
        $state,
        $country = null,
        array $phones = []
    )
    {
        return new static(
            $firstName,
            $lastName,
            $lines,
            $suburb,
            $postcode,
            $state,
            $country,
            $phones,
            $company
        );
    }

    public function equals(ValueObject $other)
    {
        throw new \Exception('Implement '.__METHOD__);
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
