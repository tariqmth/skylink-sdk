<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use BadMethodCallException;
use InvalidArgumentException;
use LogicException;
use RetailExpress\SkyLink\Sdk\Outlets\OutletId;
use RetailExpress\SkyLink\Sdk\Exceptions\Catalogue\Products\ProductHasNoQtyForOutletException;
use ValueObjects\Exception\InvalidNativeArgumentException;
use ValueObjects\Number\Integer;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class InventoryItem implements ValueObjectInterface
{
    private $managed;

    private $qtyAvailable;

    private $qtyOnOrder;

    private $outletQtys = [];

    private $productId;

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

        $qtyAvailable = isset($args[1]) ? new Integer($args[1]) : null;
        $qtyOnOrder = isset($args[2]) ? new Integer($args[2]) : null;

        $outletQtys = [];
        if (isset($args[3])) {
            $outletQtys = array_map(function (array $payload) {
                list($outletId, $qtyAvailable) = $payload;

                return OutletQty::fromNative($outletId, $qtyAvailable);
            }, $args[3]);
        }

        return new self($args[0], $qtyAvailable, $qtyOnOrder, $outletQtys);
    }

    public function __construct(
        $managed,
        Integer $qtyAvailable = null,
        Integer $qtyOnOrder = null,
        array $outletQtys = []
    ) {
        $this->assertManagedArgument($managed);
        $this->managed = boolval($managed);
        $this->qtyAvailable = $qtyAvailable;

        if (null !== $qtyOnOrder) {
            $this->assertQtyOnOrder($qtyOnOrder);
            $this->qtyOnOrder = $qtyOnOrder;
        }

        array_walk($outletQtys, function (OutletQty $outletQty) {
            $this->addOutletQty($outletQty);
        });
    }

    public function setProductId(ProductId $productId)
    {
        if (null !== $this->productId) {
            throw new LogicException('Product ID already set, cannot override.');
        }

        $this->productId = $productId;
    }

    public function isManaged()
    {
        return $this->managed;
    }

    public function getQtyAvailable()
    {
        return clone $this->qtyAvailable;
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

    public function getOutletQtys()
    {
        return array_values(array_map(function (OutletQty $outletQty) {
            return clone $outletQty;
        }, $this->outletQtys));
    }

    public function getOutletQty(OutletId $outletId)
    {
        return array_first(
            $this->getOutletQtys(),
            function ($key, OutletQty $outletQty) use ($outletId) {
                return $outletQty->getOutletId()->sameValueAs($outletId);
            },
            function () use ($outletId) {
                throw ProductHasNoQtyForOutletException::withProductIdAndOutletId(
                    $this->productId, // @todo check that the product id is set?
                    $outletId
                );
            }
        );
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

        $same = $this->isManaged() === $inventoryItem->isManaged() &&
            $this->getQty()->sameValueAs($inventoryItem->getQty()) &&
            $this->hasQtyOnOrder() === $inventoryItem->hasQtyOnOrder() &&
            true === $this->hasQtyOnOrder() && $this->getQtyOnOrder()->sameValueAs($inventoryItem->getQtyOnOrder());

        if (false === $same) {
            return false;
        }

        $ourOutletQtys = array_map('strval', $this->getOutletQtys());
        $theirOutletQtys = array_map('strval', $inventoryItem->getOutletQtys());

        return count(array_diff($ourOutletQtys, $theirOutletQtys)) === 0;
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

    private function addOutletQty(OutletQty $outletQty)
    {
        $key = (string) $outletQty;

        if (array_key_exists($key, $this->outletQtys)) {
            throw new InvalidArgumentException("Attempting to specify quantity for Outlet \"{$outletQty->getOutletId()}\" twice.");
        }

        $this->outletQtys[$key] = $outletQty;
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
