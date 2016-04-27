<?php

namespace RetailExpress\SkyLink\Sales\Payments;

use RetailExpress\SkyLink\Apis\V2 as V2Api;
use RetailExpress\SkyLink\ValueObjects\SalesChannelId;

interface PaymentMethodRepository
{
    public function all(SalesChannelId $salesChannelId);
}
