<?php

namespace RetailExpress\SkyLink\Products;

use RetailExpress\SkyLink\ValueObjects\SalesChannelId;

interface AttributeRepository
{
    public function allByCode(AttributeCode $code, SalesChannelId $salesChannelId);
}
