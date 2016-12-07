<?php

namespace RetailExpress\SkyLink\Sdk\Exceptions\Sales\Orders;

use LogicException;
use RetailExpress\SkyLink\Sdk\Sales\Fulfillments\BatchId as FulfillmentBatchId;
use RetailExpress\SkyLink\Sdk\Sales\Orders\OrderId;

class NoFulfillmentBatchWithIdException extends LogicException
{
    public static function withOrderIdAndFulfillmentBatchId(
        OrderId $orderId,
        FulfillmentBatchId $fulfillmentBatchId
    ) {
        return new self(sprintf('Order #%s does not have Fulfillment Batch #%s', $orderId, $fulfillmentBatchId));
    }
}
