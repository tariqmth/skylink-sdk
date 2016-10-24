<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Payments;

use RetailExpress\SkyLink\Sdk\ValueObjects\SalesChannelId;

interface PaymentMethodRepository
{
    public function all(SalesChannelId $salesChannelId);
}
