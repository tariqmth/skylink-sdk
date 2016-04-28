<?php

namespace RetailExpress\SkyLink\Sales\Orders;

use RetailExpress\SkyLink\ValueObjects\SalesChannelId;

interface OrderRepository
{
    public function add(SalesChannelId $salesChannelId, Order $order);
}
