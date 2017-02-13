<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use BadMethodCallException;
use ValueObjects\Exception\InvalidNativeArgumentException;
use ValueObjects\Number\Integer;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class InventoryItem implements ValueObjectInterface
{
    private $managed;

    private $qty;

    private $qtyOnOrder;

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

        $qty = isset($args[1]) ? new Integer($args[1]) : null;
        $qtyOnOrder = isset($args[2]) ? new Integer($args[2]) : null;

        return new self($args[0], $qty, $qtyOnOrder);
    }

    public function __construct($managed, Integer $qty = null, Integer $qtyOnOrder = null)
    {
        $this->assertManagedArgument($managed);
        $this->managed = boolval($managed);
        $this->qty = $qty;

        if (null !== $qtyOnOrder) {
            $this->assertQtyOnOrder($qtyOnOrder);
            $this->qtyOnOrder = $qtyOnOrder;
        }
    }

    public function isManaged()
    {
        return $this->managed;
    }

    public function getQty()
    {
        return clone $this->qty;
    }

    public function hasQtyOnOrder()
    {
        return null !== $this->qtyOnOrder;
    }

    public function getQtyOnOrder()
    {
        if ($this->hasQtyOnOrder()) {
            return clone $this->qtyOnOrder;
        }
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
            $this->getQty()->sameValueAs($inventoryItem->getQty()) &&
            $this->hasQtyOnOrder() === $inventoryItem->hasQtyOnOrder() &&
            true === $this->hasQtyOnOrder() && $this->getQtyOnOrder()->sameValueAs($inventoryItem->getQtyOnOrder());
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
        $managed = filter_var($managed, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if (null === $managed) {
            throw new InvalidNativeArgumentException($managed, array('bool'));
        }
    }

    private function assertQtyOnOrder(Integer $qtyOnOrder)
    {
        $qtyOnOrderFloat = $qtyOnOrder->toNative();

        if ($qtyOnOrderFloat < 0) {
            $message = "Qty on order cannot be less than 0, {$qtyOnOrder} given.";
            throw new InvalidArgumentException($message);
        }
    }
}
