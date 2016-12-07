<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Fulfillments;

class FulfillmentGrouper
{
    private $threshold;

    public function __construct(BatchThreshold $threshold = null)
    {
        // @codeCoverageIgnoreStart
        if (null === $threshold) {
            $threshold = BatchThreshold::createDefault();
        }
        // @codeCoverageIgnoreEnd

        $this->threshold = $threshold;
    }

    /**
     * Groups fulfillments for batching in accordance with the provided threshold.
     *
     * @param Fulfillment[] $fulfillment
     *
     * @return [Fulfillment[], Fullfillment[]]
     */
    public function groupForBatching(array $fulfillments)
    {
        $batches = [];

        // Firstly, we'll grab our timestamps
        $timestamps = array_map(function (Fulfillment $fulfillment) {
            return $fulfillment->getFulfilledAt()->getTimestamp();
        }, $fulfillments);

        // Now, we'll sort the fulfillments from oldest to newest
        asort($timestamps);
        asort($timestamps); // If all timestamps are the same, the previous function will reverse the keys, so we'll just call it twice to overcome that

        // We'll hold a reference to the last difference as well as the last index
        $firstTimestampInBatchIndex = current($timestamps);
        $currentBatchIndex = 0;
        $thresholdInSeconds = $this->threshold->getSeconds()->toNative();

        // Loop over our timestamps and batch them together so they fall within the threshold
        array_walk(
            $timestamps, function (
                $timestamp,
                $fulfillmentIndex
            ) use (&$batches, &$firstTimestampInBatchIndex, &$currentBatchIndex, $thresholdInSeconds) {
            // @codeCoverageIgnoreEnd

            // Calculate the difference between the current timestamp and the first one in the batch
            $timestampDifference = $timestamp - $firstTimestampInBatchIndex;

            // If we're inside the threshold, we'll add the fulfillment index to the current batch
            if ($timestampDifference <= $thresholdInSeconds) {
                $batches[$currentBatchIndex][] = $fulfillmentIndex;

                return;
            }

            // If we're outside of the threshold, we'll start a new batch and update the timestamp
            $batches[++$currentBatchIndex][] = $fulfillmentIndex;
            $firstTimestampInBatchIndex = $timestamp;
            }
        );

        // We'll transform our batches of fulfillment indexes into Batch instance with fulfillments in it
        return array_map(function (array $batch) use ($fulfillments) {

            // Transform our batched fulfillment indexes into the fulfillment instance
            return array_map(function ($fulfillmentIndex) use ($fulfillments) {
                return $fulfillments[$fulfillmentIndex];
            }, $batch);
        }, $batches);
    }
}
