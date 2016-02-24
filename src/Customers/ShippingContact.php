<?php

namespace RetailExpress\SkyLink\Customers;

use BadMethodCallException;
use RetailExpress\SkyLink\ValueObjects\Geography\Address;
use RetailExpress\SkyLink\ValueObjects\Person\Name;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class ShippingContact implements ValueObjectInterface
{
    use Contact;

    private $name;

    private $address;

    /**
     * Returns a new Billing Contact object from native PHP arguments.
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $companyName
     * @param string $addressLine1
     * @param string $addressLine2
     * @param string $addressCity
     * @param string $addressState
     * @param string $addressPostcode
     * @param string $addressCountry
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) !== 9) {
            throw new BadMethodCallException('You must provide exactly 9 arguments: 1) first name, 2) last name, 3) company name, 4) address line 1, 5) address line 2, 6) address city, 7) address state, 8) address postcode, 9) address country.');
        }

        $name = Name::fromNative($args[0], $args[1]);
        $companyName = new StringLiteral($args[2]);
        $address = Address::fromNative($args[3], $args[4], '', $args[5], $args[6], $args[7], $args[8]);

        return new self($name, $companyName, $address);
    }

    public function __construct(Name $name, StringLiteral $companyName, Address $address)
    {
        $this->name = $name;
        $this->companyName = $companyName;
        $this->address = $address;
    }

    /**
     * Tells whether two Billing Contact instances are equal.
     *
     * @param ValueObjectInterface $object
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $billingContact)
    {
        if (false === Util::classEquals($this, $billingContact)) {
            return false;
        }

        return $this->getName()->sameValueAs($billingContact->getName()) &&
            $this->getCompanyName()->sameValueAs($billingContact->getCompanyName()) &&
            $this->getAddress()->sameValueAs($billingContact->getAddress());
    }
}
