<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use BadMethodCallException;
use LogicException;
use RetailExpress\SkyLink\Sdk\Customers\PriceGroups\PriceGroupKey;
use RetailExpress\SkyLink\Sdk\ValueObjects\TaxRate;
use ValueObjects\Number\Real;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class PricingStructure implements ValueObjectInterface
{
    private $regularPrice;

    private $specialPrice;

    private $taxRate;

    private $priceGroupPrices = [];

    /**
     * Returns an Pricing Structure taking PHP native values as arguments.
     *
     * @return ValueObjectInterface
     *
     * @todo Special price should be optional, maybe re-order parameters?
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 3) {
            $message = 'You must provide at least 2 arguments: 1) regular price, 2) special price, 3) tax rate';
            throw new BadMethodCallException($message);
        }

        return new self(new Real($args[0]), new Real($args[1]), TaxRate::fromNative($args[2]));
    }

    public function __construct(Real $regularPrice, Real $specialPrice, TaxRate $taxRate)
    {
        $this->regularPrice = $regularPrice;
        $this->specialPrice = $specialPrice;
        $this->taxRate = $taxRate;
    }

    public function withPriceGroupPrice(PriceGroupPrice $priceGroupPrice)
    {
        $priceGroupKey = $priceGroupPrice->getKey();

        $index = (string) $priceGroupKey;
        if (array_key_exists($index, $this->priceGroupPrices)) {
            throw new LogicException("Only one price may be set per-price group, attempting to set two for \"{$priceGroupKey->getType()}\" price group {$priceGroupKey->getId()}.");
        }

        $new = clone $this;
        $new->priceGroupPrices[$index] = $priceGroupPrice;

        ksort($new->priceGroupPrices);

        return $new;
    }

    public function getRegularPrice()
    {
        return clone $this->regularPrice;
    }

    public function getSpecialPrice()
    {
        return clone $this->specialPrice;
    }

    public function getTaxRate()
    {
        return clone $this->taxRate;
    }

    public function getPriceGroupPrices()
    {
        return array_values(array_map(function (PriceGroupPrice $priceGroupPrice) {
            return clone $priceGroupPrice;
        }, $this->priceGroupPrices));
    }

    /**
     * Compare two Pricing Structure instances and tells whether they can be considered equal.
     *
     * @param ValueObjectInterface $pricingStructure
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $pricingStructure)
    {
        if (false === Util::classEquals($this, $pricingStructure)) {
            return false;
        }

        // @todo compare group prices
        $passes = $this->getRegularPrice()->sameValueAs($pricingStructure->getRegularPrice()) &&
            $this->getSpecialPrice()->sameValueAs($pricingStructure->getSpecialPrice()) &&
            $this->getTaxRate()->sameValueAs($pricingStructure->getTaxRate());
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->getRegularPrice() < $this->getSpecialPrice()) {
            return (string) $this->getRegularPrice();
        }

        return (string) $this->getSpecialPrice();
    }
}
