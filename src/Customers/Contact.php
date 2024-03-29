<?php

namespace RetailExpress\SkyLink\Sdk\Customers;

use RetailExpress\SkyLink\Sdk\ValueObjects\Geography\Address;
use RetailExpress\SkyLink\Sdk\ValueObjects\Person\Name;
use ValueObjects\StringLiteral\StringLiteral;

trait Contact
{
    /**
     * Returns the name.
     *
     * @return StringLiteral
     */
    public function getName()
    {
        return clone $this->name;
    }

    /**
     * Returns the company name.
     *
     * @return StringLiteral
     */
    public function getCompanyName()
    {
        return clone $this->companyName;
    }

    /**
     * Returns the address.
     *
     * @return StringLiteral
     */
    public function getAddress()
    {
        return clone $this->address;
    }

    /**
     * Returns the phone number.
     *
     * @return StringLiteral
     */
    public function getPhoneNumber()
    {
        return clone $this->phoneNumber;
    }

    /**
     * Returns a string representation of the billing contact.
     *
     * @return string
     */
    public function __toString()
    {
        $lines = [$this->getName()];

        if (!$this->getCompanyName()->isEmpty()) {
            $lines[] = $this->getCompanyName();
        }

        $lines[] = $this->getAddress();

        return implode("\n", $lines);
    }
}
