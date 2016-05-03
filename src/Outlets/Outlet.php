<?php

namespace RetailExpress\SkyLink\Outlets;

use RetailExpress\SkyLink\ValueObjects\Geography\Address;
use Sabre\Xml\XmlDeserializable;
use ValueObjects\StringLiteral\StringLiteral;

class Outlet implements XmlDeserializable
{
    use V2OutletDeserializer;

    private $id;

    private $name;

    private $address;

    private $phoneNumber;

    private $faxNumber;

    /**
     * Creats a new Outlet object from native PHP arguments.
     *
     * @param int    $id
     * @param string $name
     * @param string $addressLine1
     * @param string $addressLine2
     * @param string $addressLine3
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

        if (count($args) < 2) {
            throw new BadMethodCallException('You must provide at least 2 arguments: 1) id, 2) name');
        }

        $id = new OutletId($args[0]);
        $name = new StringLiteral($args[1]);
        $address = Address::fromNative(
            array_get($args, 2, ''),
            array_get($args, 3, ''),
            array_get($args, 4, ''),
            array_get($args, 5, ''),
            array_get($args, 6, ''),
            array_get($args, 7, ''),
            array_get($args, 8, '')
        );
        $phoneNumber = new StringLiteral(array_get($args, 9, ''));
        $faxNumber = new StringLiteral(array_get($args, 10, ''));

        return new self($id, $name, $address, $phoneNumber, $faxNumber);
    }

    public function __construct(
        OutletId $id,
        StringLiteral $name,
        Address $address,
        StringLiteral $phoneNumber,
        StringLiteral $faxNumber
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->phoneNumber = $phoneNumber;
        $this->faxNumber = $faxNumber;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function getFaxNumber()
    {
        return $this->faxNumber;
    }
}
