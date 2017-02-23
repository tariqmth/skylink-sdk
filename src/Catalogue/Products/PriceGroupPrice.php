<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use RetailExpress\SkyLink\Sdk\Customers\PriceGroups\PriceGroupKey;
use ValueObjects\Number\Real;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class PriceGroupPrice
{
    private $key;

    private $price;

    /**
     * Returns an Price Group Price taking PHP native values as arguments.
     *
     * @return ValueObjectInterface
     *
     * @todo Special price should be optional, maybe re-order parameters?
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 3) {
            $message = 'You must provide at least 2 arguments: 1) price group type, 2) price group id, 3) price';
            throw new BadMethodCallException($message);
        }

        $key = PriceGroupKey::fromNative($args[0], $args[1]);
        $price = new Real($args[2]);

        return new self($key, $price);
    }

    public function __construct(PriceGroupKey $key, Real $price)
    {
        $this->key = $key;
        $this->price = $price;
    }

    public function getKey()
    {
        return clone $this->key;
    }

    public function getPrice()
    {
        return clone $this->price;
    }

    /**
     * Compare two Price Group Price instances and tells whether they can be considered equal.
     *
     * @param ValueObjectInterface $priceGroupPrice
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $priceGroupPrice)
    {
        if (false === Util::classEquals($this, $priceGroupPrice)) {
            return false;
        }

        return $this->getKey()->sameValueAs($pricingStructure->getKey()) &&
            $this->getPrice()->sameValueAs($pricingStructure->getPrice());
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getPrice();
    }
}
