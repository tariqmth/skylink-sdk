<?php

namespace RetailExpress\SkyLink\Products;

use RetailExpress\SkyLink\ValueObjects\SalesChannelId;

interface AttributeRepository
{
    public function get(AttributeCode $code, SalesChannelId $salesChannelId);
}
