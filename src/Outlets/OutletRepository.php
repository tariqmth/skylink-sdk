<?php

namespace RetailExpress\SkyLink\Outlets;

use RetailExpress\SkyLink\SalesChannelId;

interface OutletRepository
{
    public function all(SalesChannelId $salesChannelId);

    public function find(OutletId $outletId);
}
