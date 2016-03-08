<?php

namespace RetailExpress\SkyLink\Products;

use ValueObjects\Number\Real;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class PricingStructure implements ValueObjectInterface
{
    private $regularPrice;

    private $specialPrice;

    /**
     * Returns an Pricing Structure taking PHP native values as arguments.
     *
     * @return ValueObjectInterface
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 2) {
            throw new BadMethodCallException('You must provide at least 2 arguments: 1) regular price, 2) special price');
        }

        return new self(new Real($args[0]), new Real($args[1]));
    }

    public function __construct(Real $regularPrice, Real $specialPrice)
    {
        $this->regularPrice = $regularPrice;
        $this->specialPrice = $specialPrice;
    }

    public function getRegularPrice()
    {
        return clone $this->regularPrice;
    }

    public function getSpecialPrice()
    {
        return clone $this->specialPrice;
    }

    /**
     * Compare two Pricing Structure instances and tells whether they can be considered equal.
     *
     * @param  ValueObjectInterface $object
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $pricingStructure)
    {
        if (false === Util::classEquals($this, $pricingStructure)) {
            return false;
        }

        return $this->getRegularPrice()->sameValueAs($pricingStructure->getRegularPrice()) &&
            $this->getSpecialPrice()->sameValueAs($pricingStructure->getSpecialPrice());
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getRegularPrice() < $this->getSpecialPrice() ? $this->getRegularPrice() : $this->getSpecialPrice();
    }
}
