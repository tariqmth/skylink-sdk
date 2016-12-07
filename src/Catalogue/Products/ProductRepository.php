<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use DateTimeImmutable;
use RetailExpress\SkyLink\Sdk\ValueObjects\SalesChannelId;

interface ProductRepository
{
    /**
     * Gets a list of product IDs on the given sales channel ID, specifying an optional flag to
     * include products that have been updated since a certain time.
     *
     * @param SalesChannelId    $salesChannelId
     * @param DateTimeImmutable $updatedSince
     */
    public function allIds(
        SalesChannelId $salesChannelId,
        DateTimeImmutable $updatedSince = null
    );

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
