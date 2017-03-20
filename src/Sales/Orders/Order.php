<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Orders;

use DateTimeImmutable;
use LogicException;
use RetailExpress\SkyLink\Sdk\Customers\BillingContact;
use RetailExpress\SkyLink\Sdk\Customers\CustomerId;
use RetailExpress\SkyLink\Sdk\Customers\ShippingContact;
use RetailExpress\SkyLink\Sdk\Exceptions\Sales\Orders\NoOrderItemWithIdException;
use RetailExpress\SkyLink\Sdk\Exceptions\Sales\Orders\NoFulfillmentBatchWithIdException;
use RetailExpress\SkyLink\Sdk\Outlets\OutletId;
use RetailExpress\SkyLink\Sdk\Sales\Fulfillments\Batch as FulfillmentBatch;
use RetailExpress\SkyLink\Sdk\Sales\Payments\Payment;
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

    private $payments = [];

    private $fulfillmentBatches = [];

    private $shippingCharge;

    private $fulfillFromOutletId;

    private $itemDeliveryMethod;

    private $itemDeliveryDriverName;

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
        if (null !== $this->getId()) {
            throw new LogicException('Order ID already set, cannot override.');
        }

        $this->id = $id;
    }

    public function setCustomerId(CustomerId $customerId)
    {
        if (null !== $this->getCustomerId()) {
            throw new LogicException('Customer ID already set, cannot override.');
        }

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

    public function withPayment(Payment $payment)
    {
        if (null === $this->getId()) {
            throw new LogicException('Payments can only be added to existing orders with an ID.');
        }

        if (false === $payment->getOrderId()->sameValueAs($this->getId())) {
            throw new LogicException(sprintf(
                'Payment is associated with Order #%s while being assigned to Order #%s.',
                $payment->getOrderId(),
                $this->getId()
            ));
        }

        $new = clone $this;
        $new->payments[] = $payment;

        return $new;
    }

    public function withFulfillmentBatch(FulfillmentBatch $fulfillmentBatch)
    {
        if (null === $this->getId()) {
            throw new LogicException('Payments can only be added to existing orders with an ID.');
        }

        $new = clone $this;
        $new->fulfillmentBatches[] = $fulfillmentBatch;

        return $new;
    }

    public function fulfillFromOutletId(OutletId $fulfillFromOutletId)
    {
        $new = clone $this;
        $new->fulfillFromOutletId = $fulfillFromOutletId;

        $new->assertFulfillmentIsCompatibleWithDeliveryMethod();

        return $new;
    }

    public function withDeliveryMethodForAllItems(ItemDeliveryMethod $itemDeliveryMethod)
    {
        $new = clone $this;
        $new->itemDeliveryMethod = $itemDeliveryMethod;

        $new->assertFulfillmentIsCompatibleWithDeliveryMethod();

        return $new;
    }

    public function withDeliveryDriverNameForAllItems(StringLiteral $itemDeliveryDriverName)
    {
        $new = clone $this;
        $new->itemDeliveryDriverName = $itemDeliveryDriverName;

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
        if (null === $this->id) {
            return null;
        }

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

    public function getItemWithId(ItemId $itemId)
    {
        return array_first(
            $this->getItems(),
            function ($key, Item $item) use ($itemId) {
                return $item->getId()->sameValueAs($itemId);
            },
            function () use ($itemId) {
                throw NoOrderItemWithIdException::withOrderIdAndItemId($this->getId(), $itemId);
            }
        );
    }

    public function getPayments()
    {
        return array_map(function (Payment $payment) {
            return clone $payment;
        }, $this->payments);
    }

    public function getLatestPayment()
    {
        $paymentsById = [];

        array_map(function (Payment $payment) use (&$paymentsById) {
            $paymentsById[(string) $payment->getId()] = $payment;
        }, $this->getPayments());

        if (0 === count($paymentsById)) {
            return null;
        }

        return $paymentsById[max(array_keys($paymentsById))];
    }

    public function isPaid()
    {
        $toPay = $this->getTotal()->toNative();

        array_map(function (Payment $payment) use (&$toPay) {
            $toPay -= $payment->getTotal()->toNative();
        }, $this->getPayments());

        // Sometimes rex might allow overpaying on an order, but to us, it's
        // still paid either way
        return $toPay <= 0;
    }

    public function getFulfillmentBatches()
    {
        return array_map(function (FulfillmentBatch $fulfillmentBatch) {
            return clone $fulfillmentBatch;
        }, $this->fulfillmentBatches);
    }

    public function getFulfillmentBatchWithId(FulfillmentBatchId $fulfillmentBatchId)
    {
        return array_first(
            $this->getFulfillmentBatches(),
            function ($key, FulfillmentBatch $fulfillmentBatch) use ($fulfillmentBatchId) {
                return $fulfillmentBatch->getId()->sameValueAs($fulfillmentBatchId);
            },
            function () use ($fulfillmentBatchId) {
                throw NoFulfillmentBatchWithIdException::withOrderIdAndFulfillmentBatchId(
                    $this->getId(),
                    $fulfillmentBatchId
                );
            }
        );
    }

    public function getFulfillments()
    {
        $fulfillments = [];

        array_map(function (FulfillmentBatch $fulfillmentBatch) use (&$fulfillments) {
            $fulfillments = array_merge($fulfillments, $fulfillmentBatch->getFulfillments());
        }, $this->getFulfillmentBatches());

        return $fulfillments;
    }

    public function isFulfilled()
    {
        foreach ($this->getItems() as $item) {
            if (false === $item->getQty()->isFulfilled()) {
                return false;
            }
        }

        return true;
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

    public function getItemDeliveryMethod()
    {
        // If no item delivery method was specified, we'll use the defualt
        // @todo is this right??
        if (!$this->specifiedItemDeliveryMethod()) {
            $this->itemDeliveryMethod = ItemDeliveryMethod::getDefault();
            $this->assertFulfillmentIsCompatibleWithDeliveryMethod();

            return $this->getItemDeliveryMethod();
        }

        return $this->itemDeliveryMethod;
    }

    public function specifiedItemDeliveryMethod()
    {
        return null !== $this->itemDeliveryMethod;
    }

    public function getItemDeliveryDriverName()
    {
        if (!$this->specifiedItemDeliveryDriverName()) {
            return null;
        }

        return clone $this->itemDeliveryDriverName;
    }

    public function specifiedItemDeliveryDriverName()
    {
        return null !== $this->itemDeliveryDriverName;
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

    private function assertFulfillmentIsCompatibleWithDeliveryMethod()
    {
        if (!$this->specifiedOutletIdToFulfillFrom() || !$this->specifiedItemDeliveryMethod()) {
            return;
        }

        $itemDeliveryMethod = $this->getItemDeliveryMethod();

        if (!$itemDeliveryMethod->isPickupLater()) {
            $message = "An outlet was specified to fulfill from, however an incompatible item delivery method \"{$itemDeliveryMethod}\" was chosen.";
            throw new LogicException($message);
        }
    }
}
