<?php

namespace RetailExpress\SkyLink;

use RetailExpress\SkyLink\ValueObject;
use InvalidArgumentException;

class Abn implements ValueObject
{
    private $number;

    private $label;

    public function __construct($number, $label = 'ABN')
    {
        $this->assertValidAbnOrAcn($number);

        $this->number = trim((string) $number);
        $this->label = $label;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function equals(ValueObject $other)
    {
        return $other->number === $this->number &&
            $other->label === $this->label;
    }

    /**
     * Validate the ABN or ACN provided is valid.
     *
     * Currently this cannot be used because Retail Express does not validate this on their end.
     *
     * @link https://www.dropbox.com/s/xsy1spftmb8o525/Screenshot%202016-02-22%2011.29.38.png?dl=0
     *
     * @param string|int $number
     *
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
