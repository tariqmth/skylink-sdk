<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Fulfillments;

use InvalidArgumentException;

class Batch
{
    private $fulfillments = [];

    public function __construct(array $fulfillments)
    {
        $this->fulfillments = array_map(function (Fulfillment $fulfillment) {
            return $fulfillment;
        }, $fulfillments);

        $this->assertAllFulfillmentsAreFromTheSameOrder();
    }

    public function getFulfillments()
    {
        return array_map(function (Fulfillment $fulfillment) {
            return clone $fulfillment;
        }, $this->fulfillments);
    }

    public function getOrderId()
    {
        return $this->getRepresentativeFulfillment()->getOrderId();
    }

    public function getFulfilledAt()
    {
        return $this->getRepresentativeFulfillment()->getFulfilledAt();
    }

    public function getId()
    {
        $fulfillmentIds = array_filter(array_map(function (Fulfillment $fulfillment) {
            if (!$fulfillment->getId()) {
                return null;
            }

            return $fulfillment->getId()->toNative();
        }, $this->getFulfillments()));

        // If no fulfillments have an ID
        if (count($fulfillmentIds) === 0) {
            return null;
        }

        sort($fulfillmentIds);

        return new BatchId(md5(implode('', $fulfillmentIds)));
    }

    private function assertAllFulfillmentsAreFromTheSameOrder()
    {
        $orderIdStrings = array_map(function (Fulfillment $fulfillment) {
            return (string) $fulfillment->getOrderId();
        }, $this->getFulfillments());

        $uniqueOrderIdStringsCount = count(array_unique($orderIdStrings));

        // If there's more than one order ID string, there's more than one order
        if ($uniqueOrderIdStringsCount > 1) {
            throw new InvalidArgumentException(sprintf(
                'All fulfillments must belong to the same order, %d orders provided.',
                $uniqueOrderIdStringsCount
            ));
        }
    }

    private function getRepresentativeFulfillment()
    {
        return $this->getFulfillments()[0];
    }
}
