<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Orders;

use DateTimeImmutable;
use RetailExpress\SkyLink\Sdk\Customers\BillingContact;
use RetailExpress\SkyLink\Sdk\Customers\CustomerId;
use RetailExpress\SkyLink\Sdk\Customers\ShippingContact;
use RetailExpress\SkyLink\Sdk\Outlets\OutletId;
use Sabre\Xml\XmlSerializable;
use ValueObjects\Number\Real;
use ValueObjects\StringLiteral\StringLiteral;

class Order implements XmlSerializable
{
    use V2OrderSerializer;

    private $id;

    private $customerId;

    private $newCustomerPassword;

    private $placedAt;

    private $status;

    private $billingContact;

    private $shippingContact;

    private $items = [];

    private $shippingCharge;

    private $fulfillFromOutletId;

    private $publicComments;

    private $privateComments;

    public static function forCustomerWithId(
        CustomerId $customerId,
        DateTimeImmutable $placedAt,
        Status $status,
        BillingContact $billingContact,
        ShippingContact $shippingContact,
        ShippingCharge $shippingCharge
    ) {
        $order = new self(
            $placedAt,
            $status,
            $billingContact,
            $shippingContact,
            $shippingCharge
        );

        $order->setCustomerId($customerId);

        return $order;
    }

    public static function forNewCustomerWithPassword(
        StringLiteral $newCustomerPassword,
        DateTimeImmutable $placedAt,
        Status $status,
        BillingContact $billingContact,
        ShippingContact $shippingContact,
        ShippingCharge $shippingCharge
    ) {
        $order = new self(
            $placedAt,
            $status,
            $billingContact,
            $shippingContact,
            $shippingCharge
        );

        $order->setNewCustomerPassword($newCustomerPassword);

        return $order;
    }

    private function __construct(
        DateTimeImmutable $placedAt,
        Status $status,
        BillingContact $billingContact,
        ShippingContact $shippingContact,
        ShippingCharge $shippingCharge
    ) {
        $this->placedAt = $placedAt;
        $this->status = $status;
        $this->billingContact = $billingContact;
        $this->shippingContact = $shippingContact;
        $this->shippingCharge = $shippingCharge;
    }

    /**
     * This is used by the repository to update the ID of the order once it's
     * been added, but I'm not sure I'm sold on this, because now there's a
     * mix of immutability vs mutability in this object.
     *
     * @todo Refactor?
     */
    public function setId(OrderId $id)
    {
        $this->id = $id;
    }

    public function setCustomerId(CustomerId $customerId)
    {
        $this->customerId = $customerId;
    }

    public function setNewCustomerPassword(StringLiteral $newCustomerPassword)
    {
        $this->newCustomerPassword = $newCustomerPassword;
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

    public function getId()
    {
        return clone $this->id;
    }

    public function getCustomerId()
    {
        if (null === $this->customerId) {
            return null;
        }

        return clone $this->customerId;
    }

    public function getNewCustomerPassword()
    {
        if (null === $this->newCustomerPassword) {
            return null;
        }

        return clone $this->newCustomerPassword;
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
            return null;
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
            return null;
        }

        return clone $this->publicComments;
    }

    public function getPrivateComments()
    {
        if (null === $this->privateComments) {
            return null;
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
