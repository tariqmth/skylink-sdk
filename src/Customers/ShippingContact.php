<?php

namespace RetailExpress\SkyLink\Customers;

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

    private $phoneNumber;

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
     * @param string $phoneNumber
     */
    public static function fromNative()
    {
        $args = func_get_args();

        $name = Name::fromNative(array_get($args, 0, ''), array_get($args, 1, ''));
        $companyName = new StringLiteral(array_get($args, 2, ''));
        $address = Address::fromNative(
            array_get($args, 3, ''),
            array_get($args, 4, ''),
            '',
            array_get($args, 5, ''),
            array_get($args, 6, ''),
            array_get($args, 7, ''),
            array_get($args, 8, '')
        );
        $phoneNumber = new StringLiteral(array_get($args, 9, ''));

        return new self(
            $name,
            $companyName,
            $address,
            $phoneNumber
        );
    }

    public function __construct(Name $name, StringLiteral $companyName, Address $address, StringLiteral $phoneNumber)
    {
        $this->name = $name;
        $this->companyName = $companyName;
        $this->address = $address;
        $this->phoneNumber = $phoneNumber;
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
            $this->getAddress()->sameValueAs($billingContact->getAddress()) &&
            $this->getPhoneNumber()->sameValueAs($billingContact->getPhoneNumber());
    }
}
