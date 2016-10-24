<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use RetailExpress\SkyLink\Sdk\ValueObjects\SalesChannelId;

interface ProductRepository
{
    /**
     * Finds a product, given it's Product ID and a Sales Channel ID.
     *
     * @param ProductId      $productId
     * @param SalesChannelId $salesChannelId
     *
     * @return Product An implementation of Product, be it a Simple Product or Matrix
     */
    public function find(
        ProductId $productId,
        SalesChannelId $salesChannelId
    );
}
