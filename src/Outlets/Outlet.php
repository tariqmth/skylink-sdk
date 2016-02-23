<?php

namespace RetailExpress\SkyLink\Outlets;

use RetailExpress\SkyLink\Company;

class Outlet
{
    private $id;

    private $name;

    private $address;

    private $company;

    private function __construct(OutletId $id, $name, Address $address, Company $company = null)
    {
        $this->id = $id;
        $this->name = (string) $name;
        $this->address = $address;
        $this->company = $company;
    }

    public static function existing(OutletId $id, $name, Address $address, Company $company = null)
    {
        return new self($id, $name, $address, $company);
    }
}
