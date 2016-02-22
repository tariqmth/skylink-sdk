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

    /**
     * @todo Switch the order of postcode and state
     */
    public function __construct(
        $firstName,
        $lastName,
        $lines = [],
        $suburb = null,
        $postcode = null,
        $state = null,
        $country = null,
        array $phones = [],
        Company $company = null
    ) {
        $this->firstName = trim((string) $firstName);
        $this->lastName = trim((string) $lastName);

        $lines = (array) $lines;
        $this->sanitiseLines($lines);
        $this->lines = $lines;

        $this->suburb = isset($suburb) ? trim((string) $suburb) : null;
        $this->postcode = isset($postcode) ? trim((string) $postcode) : null;
        $this->state = isset($state) ? trim((string) $state) : null;
        $this->country = isset($country) ? trim((string) $country) : null;

        $this->validateAndSanitisePhones($phones);
        $this->phones = $phones;

        $this->company = isset($company) ? $company : null;
    }

    public static function forIndividual(
        $firstName,
        $lastName,
        $lines = [],
        $suburb = null,
        $postcode = null,
        $state = null,
        $country = null,
        array $phones = []
    ) {
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
        $lines = [],
        $suburb = null,
        $postcode = null,
        $state = null,
        $country = null,
        array $phones = []
    ) {
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

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getLines()
    {
        return $this->lines;
    }

    public function getSuburb()
    {
        return $this->suburb;
    }

    public function getPostcode()
    {
        return $this->postcode;
    }

    public function getState()
    {
        return $this->state;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getPhones()
    {
        return $this->phones;
    }

    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Formats the address in a standard Australian format.
     *
     * An example of this is as follows:
     *
     *   {company name}
     *   {line 1}
     *   {line 2}
     *   {suburb} {state} {postcode}
     *   {country}
     *
     * @return string
     */
    public function toString()
    {
        $address = [];

        if ($this->company !== null) {
            $address[] = $this->company->getName();
        }

        // These may be null values, so we'll filter the final address array
        foreach ($this->lines as $line) {
            array_push($address, $line);
        }

        $localityLine = array_filter([$this->suburb, $this->state, $this->postcode]);
        $address[] = implode(' ', $localityLine);

        $address[] = $this->country;

        return implode("\n", $address);
    }

    public function equals(ValueObject $other)
    {
        throw new \Exception('Implement '.__METHOD__);
    }

    private function sanitiseLines(array &$lines)
    {
        $lines = array_map(function ($line) {
            return trim((string) $line);
        }, array_filter($lines));

        // There is a maximum of two lines
        $lines = array_slice($lines, 0, 2);
    }

    private function validateAndSanitisePhones(array &$phones)
    {
        foreach ($phones as $type => $number) {
            if (!in_array($type, self::$phoneTypes)) {
                throw new InvalidArgumentException("Phone type \"{$type}\" is not valid.");
            }

            $phones[$type] = trim((string) $number);
        }
    }
}
