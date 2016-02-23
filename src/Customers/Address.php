<?php

namespace RetailExpress\SkyLink\Customers;

use RetailExpress\SkyLink\Address as BaseAddress;
use RetailExpress\SkyLink\Company;
use RetailExpress\SkyLink\ValueObject;

abstract class Address extends BaseAddress implements ValueObject
{
    private $firstName;

    private $lastName;

    private $company;

    protected function __construct(
        $lines = [],
        $suburb = null,
        $postcode = null,
        $state = null,
        $country = null,
        array $phones = [],
        $firstName,
        $lastName,
        Company $company = null
    ) {
        parent::__construct($lines, $suburb, $postcode, $state, $country, $phones);

        $this->firstName = trim((string) $firstName);
        $this->lastName = trim((string) $lastName);

        $this->company = $company;
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
            $lines,
            $suburb,
            $postcode,
            $state,
            $country,
            $phones,
            $firstName,
            $lastName
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
            $lines,
            $suburb,
            $postcode,
            $state,
            $country,
            $phones,
            $firstName,
            $lastName,
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
     *   {line 3}
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
}
