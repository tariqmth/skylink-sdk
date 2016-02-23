<?php

namespace RetailExpress\SkyLink;

use InvalidArgumentException;

abstract class Address implements ValueObject
{
    private static $phoneTypes = ['phone', 'mobile', 'fax'];

    protected $lines = [];

    protected $suburb;

    protected $postcode;

    protected $state;

    protected $country;

    protected $phones = [];

    /**
     * @todo Switch the order of postcode and state
     */
    protected function __construct(
        $lines = [],
        $suburb = null,
        $postcode = null,
        $state = null,
        $country = null,
        array $phones = []
    ) {
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

    public function equals(ValueObject $other)
    {
        return $other->firstName === $this->firstName &&
            $other->lastName === $this->lastName &&
            $other->lines === $this->lines &&
            $other->suburb === $this->suburb &&
            $other->postcode === $this->postcode &&
            $other->state === $this->state &&
            $other->country === $this->country &&
            $other->phones === $this->phones &&
            $other->company === $this->company;
    }

    /**
     * Formats the address in a standard Australian format.
     *
     * An example of this is as follows:
     *
     *   {company name}
     *   {line 1}
     *   {line 2}
     *   {line 3}
     *   {suburb} {state} {postcode}
     *   {country}
     *
     * @return string
     */
    public function toString()
    {
        $address = $this->getAddressParts();

        return implode("\n", $address);
    }

    protected function getAddressParts()
    {
        $address = [];

        // These may be null values, so we'll filter the final address array
        foreach ($this->lines as $line) {
            array_push($address, $line);
        }

        $localityLine = array_filter([$this->suburb, $this->state, $this->postcode]);
        $address[] = implode(' ', $localityLine);

        $address[] = $this->country;

        return $address;
    }

    private function sanitiseLines(array &$lines)
    {
        $lines = array_map(function ($line) {
            return trim((string) $line);
        }, array_filter($lines));
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
