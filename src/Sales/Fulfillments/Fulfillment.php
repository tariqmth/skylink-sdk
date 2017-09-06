<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Fulfillments;

use LogicException;
use RetailExpress\SkyLink\Sdk\Sales\Orders\OrderId;
use RetailExpress\SkyLink\Sdk\Sales\Orders\ItemId;
use ValueObjects\Number\Real;

class Fulfillment
{
    private $orderId;

    private $orderItemId;

    private $fulfilledAt;

    private $qty;

    private $id;

    public static function fromNative($orderId, $orderItemId, $fulfilledAt, $qty)
    {
        return new self(
            new OrderId((string) $orderId),
            new ItemId($orderItemId),
            new DateTimeImmutable("@{$fulfilledAt}"),
            new Real($qty)
        );
    }

    public function __construct(
        OrderId $orderId,
        ItemId $orderItemId,
        DateTimeImmutable $fulfilledAt,
        Real $qty
    ) {
        $this->orderId = $orderId;
        $this->orderItemId = $orderItemId;
        $this->fulfilledAt = $fulfilledAt;
        $this->qty = $qty;
    }

    public function setId(FulfillmentId $id)
    {
        if (null !== $this->id) {
            throw new LogicException('Fulfillment ID already set, cannot override.');
        }

        $this->id = $id;
    }

    public function getOrderId()
    {
        return clone $this->orderId;
    }

    public function getOrderItemId()
    {
        return clone $this->orderItemId;
    }

    public function getFulfilledAt()
    {
        return clone $this->fulfilledAt;
    }

    public function getQty()
    {
        return clone $this->qty;
    }

    public function getId()
    {
        if (null === $this->id) {
            return null;
        }

        return clone $this->id;
    }
}
