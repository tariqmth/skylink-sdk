<?php

namespace RetailExpress\SkyLink\Customers;

use RetailExpress\SkyLink\ValueObject;
use InvalidArgumentException;

class Abn implements ValueObject
{
    private $number;

    public function __construct($number)
    {
        $this->assertValidAbnOrAcn($number);

        $this->number = $number;
    }

    public function equals(ValueObject $other)
    {
        return $other->number === $this->number;
    }

    private function assertValidAbnOrAcn($number)
    {
        if (!AbnValidator::isValidAbnOrAcn($number)) {
            throw new InvalidArgumentException("\"{$number}\" is not a valid ABN or ACN.");
        }
    }
}
