<?php

namespace RetailExpress\SkyLink\Sdk\Customers;

use RetailExpress\SkyLink\Sdk\ValueObjects\Geography\Address;
use RetailExpress\SkyLink\Sdk\ValueObjects\Person\Name;
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
     * Returns a new Shipping Contact object from native PHP arguments.
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
     * Tells whether two Shipping Contact instances are equal.
     *
     * @param ValueObjectInterface $shippingContact
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $shippingContact)
    {
        if (false === Util::classEquals($this, $shippingContact)) {
            return false;
        }

        return $this->getName()->sameValueAs($shippingContact->getName()) &&
            $this->getCompanyName()->sameValueAs($shippingContact->getCompanyName()) &&
            $this->getAddress()->sameValueAs($shippingContact->getAddress()) &&
            $this->getPhoneNumber()->sameValueAs($shippingContact->getPhoneNumber());
    }
}
