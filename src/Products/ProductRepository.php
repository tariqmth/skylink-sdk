<?php

namespace RetailExpress\SkyLink\Products;

use RetailExpress\SkyLink\ValueObjects\SalesChannelId;

interface ProductRepository
{
    public function find(
        ProductId $productId,
        SalesChannelId $salesChannelId
    );
}
