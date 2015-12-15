<?php

namespace RetailExpress\SkyLink\Products;

use RetailExpress\SkyLink\Customers\CustomerGroupId;
use RetailExpress\SkyLink\SalesChannelId;

interface ProductRepository
{
    public function find(
        ProductId $productId,
        SalesChannelId $salesChannelId,
        CustomerGroupId $customerGroupId = null
    );
}
