<?php

namespace RetailExpress\SkyLink\Outlets;

use RetailExpress\SkyLink\ValueObjects\Geography\Address;
use ValueObjects\StringLiteral\StringLiteral;

class Outlet
{
    private $id;

    private $name;

    private $address;

    private $phoneNumber;

    private $faxNumber;

    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 2) {
            throw new BadMethodCallException('You must provide at least 2 arguments: 1) id, 2) name');
        }
    }

    public function __construct(OutletId $id, StringLiteral $name, Address $address, StringLiteral $phoneNumber, StringLiteral $faxNumber)
    {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->phoneNumber = $phoneNumber;
        $this->faxNumber = $faxNumber;
    }
}
