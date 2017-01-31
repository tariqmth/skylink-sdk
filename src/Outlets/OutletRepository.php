<?php

namespace RetailExpress\SkyLink\Sdk\Outlets;

use RetailExpress\SkyLink\Sdk\ValueObjects\SalesChannelId;

interface OutletRepository
{
    public function all(SalesChannelId $salesChannelId);

    public function find(OutletId $outletId, SalesChannelId $salesChannelId);
}
