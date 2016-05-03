<?php

namespace RetailExpress\SkyLink\Sales\Orders;

use ValueObjects\Number\Real;
use ValueObjects\ValueObjectInterface;

class ItemQty implements ValueObjectInterface
{
    private $ordered;

    private $fulfilled;

    /**
     * Returns an Item Qty object taking PHP native values as arguments.
     *
     * @return AttributeOption
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 1) {
            throw new BadMethodCallException('You must provide at least 1 argument: 1) qty ordered');
        }

        $ordered = new Real($args[0]);
        $fulfilled = isset($args[1]) ? new Real($args[1]) : null;

        return new self($ordered, $fulfilled);
    }

    public function __construct(Real $ordered, Real $fulfilled = null)
    {
        $this->ordered = $ordered;

        if (null === $fulfilled) {
            return;
        }

        $this->assertValidFulfilled($fulfilled);
        $this->fulfilled = $fulfilled;
    }

    public function getOrdered()
    {
        return clone $this->ordered;
    }

    public function getFulfilled()
    {
        return clone $this->fulfilled;
    }

    /**
     * Tells whether two Item Qty instances are equal.
     *
     * @param ValueObjectInterface $itemQty
     *itemQty
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $itemQty)
    {
        if (false === Util::classEquals($this, $itemQty)) {
            return false;
        }

        return $this->getOrdered()->sameValueAs($itemQty->getOrdered()) &&
            $this->getFulfilled()->sameValueAs($itemQty->getFulfilled());
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->ordered;
    }

    private function assertValidFulfilled(Real $fulfilled)
    {
        $fulfilledFloat = $fulfilled->toNative();
        $orderedFloat = $this->getOrdered()->toNative();

        // All we need to do to make sure the qty fulfilled is valid (for a positive qty or negative qty)
        // is make sure the difference between the two is between 0 and the qty ordered, anything else
        // is an invalid fulfillment amount based on the ordered amount
        if ($orderedFloat - $fulfilledFloat < 0 || $orderedFloat - $fulfilledFloat > $orderedFloat) {
            $message = "Cannot fulfill {$fulfilledFloat} when {$orderedFloat} item(s) were ordered.";
            throw new InvalidArgumentException($message);
        }
    }
}
