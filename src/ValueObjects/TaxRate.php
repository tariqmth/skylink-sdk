<?php

namespace RetailExpress\SkyLink\Sdk\ValueObjects;

use InvalidArgumentException;
use ValueObjects\Number\Real;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class TaxRate implements ValueObjectInterface
{
    private $rate;

    /**
     * Returns a new Tax Rate object from a PHP native, either represented as a
     * number (decimal).
     *
     * @return self
     */
    public static function fromNative()
    {
        $rate = func_get_arg(0);

        if (ends_with($rate, '%')) {
            return self::fromPercentage($rate);
        }

        return new self(new Real($rate));
    }

    public static function fromPercentage($percentage)
    {
        $percentage = rtrim($percentage, '%');

        return new self(new Real($percentage / 100));
    }

    public function __construct(Real $rate)
    {
        $this->assertValidRate($rate);
        $this->rate = $rate;
    }

    public function getRate()
    {
        return clone $this->rate;
    }

    public function isTaxable()
    {
        return $this->getRate()->toNative() > 0;
    }

    /**
     * Tells whether two Integer are equal by comparing their values.
     *
     * @param ValueObjectInterface $integer
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $taxRate)
    {
        if (false === Util::classEquals($this, $taxRate)) {
            return false;
        }

        return $this->toNative() === $taxRate->toNative();
    }

    /**
     * Returns the value of the newsletter subscription.
     *
     * @return int
     */
    public function toNative()
    {
        return $this->rate->toNative();
    }

    /**
     * Returns a string representation of the newsletter subscription.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->toNative();
    }

    private function assertValidRate(Real $rate)
    {
        if ($rate->toNative() >= 1 || $rate->toNative() < 0) {
            $message = "Tax rate {$rate} must be expressed greater than (or equal to) 0 and less than 1.";
            throw new InvalidArgumentException($message);
        }
    }
}
