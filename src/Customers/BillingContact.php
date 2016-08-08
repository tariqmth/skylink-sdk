<?php

namespace RetailExpress\SkyLink\Customers;

use BadMethodCallException;
use RetailExpress\SkyLink\ValueObjects\Geography\Address;
use RetailExpress\SkyLink\ValueObjects\Person\Name;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;
use ValueObjects\Web\EmailAddress;

class BillingContact implements ValueObjectInterface
{
    use Contact;

    private $name;

    private $emailAddress;

    private $address;

    private $phoneNumber;

    private $faxNumber;

    /**
     * Returns a new Billing Contact object from native PHP arguments.
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $emailAddress
     * @param string $companyName
     * @param string $addressLine1
     * @param string $addressLine2
     * @param string $addressCity
     * @param string $addressState
     * @param string $addressPostcode
     * @param string $addressCountry
     * @param string $phoneNumber
     * @param string $faxNumber
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 3) {
            $message = 'You must provide at least 3 arguments: 1) first name, 2) last name, 3) email address';
            throw new BadMethodCallException($message);
        }

        $name = Name::fromNative($args[0], $args[1]);
        $emailAddress = new EmailAddress($args[2]);
        $companyName = new StringLiteral(array_get($args, 3, ''));
        $address = Address::fromNative(
            array_get($args, 4, ''),
            array_get($args, 5, ''),
            '',
            array_get($args, 6, ''),
            array_get($args, 7, ''),
            array_get($args, 8, ''),
            array_get($args, 9, '')
        );
        $phoneNumber = new StringLiteral(array_get($args, 10, ''));
        $faxNumber = new StringLiteral(array_get($args, 11, ''));

        return new self(
            $name,
            $emailAddress,
            $companyName,
            $address,
            $phoneNumber,
            $faxNumber
        );
    }

    public function __construct(
        Name $name,
        EmailAddress $emailAddress,
        StringLiteral $companyName,
        Address $address,
        StringLiteral $phoneNumber,
        StringLiteral $faxNumber
    ) {
        $this->name = $name;
        $this->emailAddress = $emailAddress;
        $this->companyName = $companyName;
        $this->address = $address;
        $this->phoneNumber = $phoneNumber;
        $this->faxNumber = $faxNumber;
    }

    /**
     * Returns the email address.
     *
     * @return StringLiteral
     */
    public function getEmailAddress()
    {
        return clone $this->emailAddress;
    }

    /**
     * Returns the fax number.
     *
     * @return StringLiteral
     */
    public function getFaxNumber()
    {
        return clone $this->faxNumber;
    }

    /**
     * Tells whether two Billing Contact instances are equal.
     *
     * @param ValueObjectInterface $billingContact
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $billingContact)
    {
        if (false === Util::classEquals($this, $billingContact)) {
            return false;
        }

        return $this->getName()->sameValueAs($billingContact->getName()) &&
            $this->getEmailAddress()->sameValueAs($billingContact->getEmailAddress()) &&
            $this->getCompanyName()->sameValueAs($billingContact->getCompanyName()) &&
            $this->getAddress()->sameValueAs($billingContact->getAddress()) &&
            $this->getPhoneNumber()->sameValueAs($billingContact->getPhoneNumber()) &&
            $this->getFaxNumber()->sameValueAs($billingContact->getFaxNumber());
    }
}
