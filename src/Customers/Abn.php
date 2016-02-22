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

        $this->number = trim((string) $number);
    }

    public function equals(ValueObject $other)
    {
        return $other->number === $this->number;
    }

    /**
     * Validate the ABN or ACN provided is valid.
     *
     * Currently this cannot be used because Retail Express does not validate this on their end.
     *
     * @link https://www.dropbox.com/s/xsy1spftmb8o525/Screenshot%202016-02-22%2011.29.38.png?dl=0
     *
     * @param string|int $number
     * @throws InvalidArgumentException
     */
    private function assertValidAbnOrAcn($number)
    {
        return;

        if (!AbnValidator::isValidAbnOrAcn($number)) {
            throw new InvalidArgumentException("\"{$number}\" is not a valid ABN or ACN.");
        }
    }
}
