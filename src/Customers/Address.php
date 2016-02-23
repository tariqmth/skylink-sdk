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

    public function equals(ValueObject $other)
    {
        return parent::equals($other) &&
            $other->firstName === $this->firstName &&
            $other->lastName === $this->lastName &&
            $other->company === $this->company;
    }

    protected function getAddressParts()
    {
        $address = parent::getAddressParts();

        if ($this->company !== null) {
            array_unshift($address, $this->company->getName());
        }

        return $address;
    }
}
