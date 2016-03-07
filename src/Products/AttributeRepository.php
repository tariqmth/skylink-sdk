<?php

namespace RetailExpress\SkyLink\Products;

use RetailExpress\SkyLink\ValueObjects\SalesChannelId;

interface AttributeRepository
{
    public function allValuesByCode(AttributeCode $code, SalesChannelId $salesChannelId);
}
