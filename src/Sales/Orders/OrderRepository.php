<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Orders;

use RetailExpress\SkyLink\Sdk\ValueObjects\SalesChannelId;

interface OrderRepository
{
    public function add(SalesChannelId $salesChannelId, Order $order);

    public function get(OrderId $orderId);
}
