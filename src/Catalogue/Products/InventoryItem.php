<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use BadMethodCallException;
use ValueObjects\Number\Integer;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class InventoryItem implements ValueObjectInterface
{
    private $managed;

    private $qty;

    /**
     * Returns an Inventory Item taking PHP native values as arguments.
     *
     * @return ValueObjectInterface
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 1) {
            throw new BadMethodCallException('You must provide at least 1 argument: 1) managed');
        }

        return new self($args[0], new Integer($args[1]));
    }

    public function __construct($managed, Integer $qty = null)
    {
        $this->assertManagedArgument($managed);
        $this->managed = boolval($managed);
        $this->qty = $qty;
    }

    public function isManaged()
    {
        return $this->managed;
    }

    public function getQty()
    {
        return clone $this->qty;
    }

    /**
     * Compare two Inventory Item instances and tells whether they can be considered equal.
     *
     * @param ValueObjectInterface $inventoryItem
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $inventoryItem)
    {
        if (false === Util::classEquals($this, $inventoryItem)) {
            return false;
        }

        return $this->isManaged() === $inventoryItem->isManaged() &&
            $this->getQty()->sameValueAs($inventoryItem->getQty());
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getQty()->toNative();
    }

    private function assertManagedArgument($managed)
    {
        $managed = filter_var($managed, FILTER_VALIDATE_INT);

        if (false === $managed) {
            throw new InvalidNativeArgumentException($managed, array('bool'));
        }
    }
}
