<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Fulfillments;

use InvalidArgumentException;

class Batch
{
    private $threshold;

    private $fulfillments = [];

    public function __construct(BatchThreshold $threshold, array $fulfillments)
    {
        $this->threshold = $threshold;

        $this->fulfillments = array_map(function (Fulfillment $fulfillment) {
            return $fulfillment;
        }, $fulfillments);

        $this->assertAllFulfillmentsAreFromTheSameOrder();
        $this->assertFulfillmentsAreWitihnThreshold();
    }

    public function getThreshold()
    {
        return clone $this->threshold;
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
        $idStrings = array_filter(array_map(function (Fulfillment $fulfillment) {
            return $fulfillment->getId() ?: null;
        }, $this->getFulfillments()));

        // If no fulfillments have an ID
        if (count($idStrings) === 0) {
            return null;
        }

        return new BatchId(md5(implode('', $idStrings)));
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

    private function assertFulfillmentsAreWitihnThreshold()
    {
        // Grab the fulfillment timestamps
        $fulfillmentTimestamps = array_map(function (Fulfillment $fulfillment) {
            return $fulfillment->getFulfilledAt()->getTimestamp();
        }, $this->getFulfillments());

        // Grab the difference of the highest and lowest timestamps
        $highest = max($fulfillmentTimestamps);
        $lowest = min($fulfillmentTimestamps);

        $difference = $highest - $lowest;
        $thresholdSeconds = $this->getThreshold()->getSeconds()->toNative();

        if ($difference > $thresholdSeconds) {
            throw new InvalidArgumentException(sprintf(
                'There is a difference of %d second(s) in given fulfillments, whereas the batch threshold only allows %s second(s).',
                $difference,
                $thresholdSeconds
            ));
        }
    }

    private function getRepresentativeFulfillment()
    {
        return $this->getFulfillments()[0];
    }
}
