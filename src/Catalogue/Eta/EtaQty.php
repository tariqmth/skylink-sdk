<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Eta;

use BadMethodCallException;
use InvalidArgumentException;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class EtaQty implements ValueObjectInterface
{
    private $qty;

    /**
     * Returns an ETA Qty object taking PHP native values as arguments.
     *
     * @return AttributeOption
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 1) {
            throw new BadMethodCallException('You must provide at least 1 argument: 1) qty (greater than 0)');
        }

        return new self($args[0]);
    }

    public function __construct($qty)
    {
        $this->assertAndSanitiseQty($qty);

        $this->qty = $qty;
    }

    public function toNative()
    {
        return $this->qty;
    }

    /**
     * Tells whether two ETAE Qty instances are equal.
     *
     * @param ValueObjectInterface $etaQty
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $etaQty)
    {
        if (false === Util::classEquals($this, $etaQty)) {
            return false;
        }

        return $this->toNative()->sameValueAs($etaQty->toNative());
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->qty;
    }

    private function assertAndSanitiseQty(&$qty)
    {
        $qty = intval($qty);

        if ($qty <= 0) {
            $message = "Qty must be greater than 0, {$qty} given.";
            throw new InvalidArgumentException($message);
        }
    }
}
