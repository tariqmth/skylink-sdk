<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use BadMethodCallException;
use RetailExpress\SkyLink\Sdk\Outlets\OutletId;
use ValueObjects\Number\Integer;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class OutletQty implements ValueObjectInterface
{
    private $outletId;

    private $qty;

    /**
     * Returns a object taking PHP native value(s) as argument(s).
     *
     * @return ValueObjectInterface
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 2) {
            throw new BadMethodCallException('You must provide at least 2 argument: 1) outlet id, 2) qty');
        }

        return new self(new OutletId($args[0]), new Integer($args[1]));
    }

    public function __construct(OutletId $outletId, Integer $qty)
    {
        $this->outletId = $outletId;
        $this->qty = $qty;
    }

    public function getOutletId()
    {
        return clone $this->outletId;
    }

    public function getQty()
    {
        return clone $this->qty;
    }

    /**
     * Compare two ValueObjectInterface and tells whether they can be considered equal
     *
     * @param  ValueObjectInterface $object
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $outletQty)
    {
        if (false === Util::classEquals($this, $outletQty)) {
            return false;
        }

        return $this->getOutletId()->sameValueAs($outletQty->getOutletId()) &&
            $this->getQty()->sameValueAs($outletQty->getQty());
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s:%s', $this->getOutletId(), $this->getQty());
    }
}
