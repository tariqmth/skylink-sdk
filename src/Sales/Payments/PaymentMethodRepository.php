<?php

namespace RetailExpress\SkyLink\Sales\Payments;

use RetailExpress\SkyLink\ValueObjects\SalesChannelId;

interface PaymentMethodRepository
{
    public function all(SalesChannelId $salesChannelId);
}
