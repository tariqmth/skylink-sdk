<?php

namespace RetailExpress\SkyLink\Sales\Orders;

use BadMethodCallException;
use RetailExpress\SkyLink\Catalogue\Products\ProductId;
use RetailExpress\SkyLink\ValueObjects\TaxRate;
use ValueObjects\Number\Real;

class Item
{
    use TaxablePrice;

    private $productId;

    private $qty;

    private $price;

    private $taxRate;

    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 5) {
            // @codingStandardsIgnoreStart
            $message = 'You must provide at least 5 arguments: 1) product id, 2) qty ordered, 3) qty fulfilled, 4) price, 5) tax rate';
            // @codingStandardsIgnoreEnd

            throw new BadMethodCallException($message);
        }

        $productId = new ProductId($args[0]);
        $qty = ItemQty::fromNative($args[1], $args[2]);
        $price = new Real($args[3]);
        $taxRate = TaxRate::fromNative($args[4]);

        return new self($productId, $qty, $price, $taxRate);
    }

    public function __construct(ProductId $productId, ItemQty $qty, Real $price, TaxRate $taxRate)
    {
        $this->productId = $productId;
        $this->qty = $qty;
        $this->price = $price;
        $this->taxRate = $taxRate;
    }

    public function getProductId()
    {
        return clone $this->productId;
    }

    public function getQty()
    {
        return clone $this->qty;
    }

    public function getTotal()
    {
        $qtyOrderedFloat = $this->getQty()->getOrdered()->toNative();
        $priceFloat = $this->getPrice()->toNative();

        return new Real($qtyOrderedFloat * $priceFloat);
    }

    public function getTotalExclTax()
    {
        $qtyOrderedFloat = $this->getQty()->getOrdered()->toNative();
        $priceExclTaxFloat = $this->getPriceExclTax()->toNative();

        return new Real($qtyOrderedFloat * $priceExclTaxFloat);
    }
}
