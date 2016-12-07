<?php

namespace RetailExpress\SkyLink\Sdk\Exceptions\Sales\Orders;

use LogicException;
use RetailExpress\SkyLink\Sdk\Sales\Orders\OrderId;
use RetailExpress\SkyLink\Sdk\Sales\Orders\ItemId;

class NoOrderItemWithIdException extends LogicException
{
    public static function withOrderIdAndItemId(OrderId $orderId, ItemId $itemId)
    {
        return new self(sprintf('Order #%s does not have Item #%s', $orderId, $itemId));
    }
}
