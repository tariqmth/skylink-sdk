<?php

namespace RetailExpress\SkyLink\Sales\Orders;

use DateTimeImmutable;
use RetailExpress\SkyLink\Customers\BillingContact;
use RetailExpress\SkyLink\Customers\ShippingContact;
use RetailExpress\SkyLink\Outlets\OutletId;
use Sabre\Xml\XmlSerializable;
use ValueObjects\Number\Real;
use ValueObjects\StringLiteral\StringLiteral;

class Order implements XmlSerializable
{
    use V2OrderSerializer;

    private $placedAt;

    private $status;

    private $billingContact;

    private $shippingContact;

    private $items = [];

    private $shippingCharge;

    private $fulfillFromOutletId;

    private $publicComments;

    private $privateComments;

    public function __construct(DateTimeImmutable $placedAt, Status $status, BillingContact $billingContact, ShippingContact $shippingContact, ShippingCharge $shippingCharge)
    {
        $this->placedAt = $placedAt;
        $this->status = $status;
        $this->billingContact = $billingContact;
        $this->shippingContact = $shippingContact;
        $this->shippingCharge = $shippingCharge;
    }

    public function withItem(Item $item)
    {
        $new = clone $this;
        $new->items[] = $item;

        return $new;
    }

    public function fulfillFromOutletId(OutletId $fulfillFromOutletId)
    {
        $new = clone $this;
        $new->fulfillFromOutletId = $fulfillFromOutletId;

        return $new;
    }

    public function withPublicComments(StringLiteral $publicComments)
    {
        $new = clone $this;
        $new->publicComments = $publicComments;

        return $new;
    }

    public function withPrivateComments(StringLiteral $privateComments)
    {
        $new = clone $this;
        $new->privateComments = $privateComments;

        return $new;
    }

    public function getPlacedAt()
    {
        return clone $this->placedAt;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getBillingContact()
    {
        return clone $this->billingContact;
    }

    public function getShippingContact()
    {
        return clone $this->shippingContact;
    }

    public function getItems()
    {
        return array_map(function (Item $item) {
            return clone $item;
        }, $this->items);
    }

    public function getShippingCharge()
    {
        return clone $this->shippingCharge;
    }

    public function getOutletIdToFulfillFrom()
    {
        if (!$this->specifiedOutletIdToFulfillFrom()) {
            return;
        }

        return clone $this->fulfillFromOutletId;
    }

    public function specifiedOutletIdToFulfillFrom()
    {
        return null !== $this->fulfillFromOutletId;
    }

    public function getPublicComments()
    {
        if (null === $this->publicComments) {
            return;
        }

        return clone $this->publicComments;
    }

    public function getPrivateComments()
    {
        if (null === $this->privateComments) {
            return;
        }

        return clone $this->privateComments;
    }

    public function getTotal()
    {
        $total = 0;

        foreach ($this->getItems() as $item) {
            $total += $item->getTotal()->toNative();
        }

        $total += $this->getShippingCharge()->getPrice()->toNative();

        return new Real($total);
    }

    public function getTotalExclTax()
    {
        $total = 0;

        foreach ($this->getItems() as $item) {
            $total += $item->getTotalExclTax()->toNative();
        }

        $total += $this->getShippingCharge()->getPriceExclTax()->toNative();

        return new Real($total);
    }
}
