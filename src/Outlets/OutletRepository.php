<?php

namespace RetailExpress\SkyLink\Outlets;

use RetailExpress\SkyLink\ValueObjects\SalesChannelId;

interface OutletRepository
{
    public function all(SalesChannelId $salesChannelId);
}
