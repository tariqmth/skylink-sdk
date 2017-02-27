<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use DateTimeImmutable;
use RetailExpress\SkyLink\Sdk\ValueObjects\SalesChannelId;

interface ProductRepository
{
    /**
     * Gets a list of product IDs on the given sales channel ID.
     *
     * @param SalesChannelId    $salesChannelId
     */
    public function allIds(SalesChannelId $salesChannelId);

    /**
     * Finds a product, given it's Product ID and a Sales Channel ID. This method may return a
     * composite product (such as a Matrix) that contains the requested product.
     *
     * @param ProductId      $productId
     * @param SalesChannelId $salesChannelId
     *
     * @return Product An implementation of Product, be it a Simple Product or Composite Product
     */
    public function find(
        ProductId $productId,
        SalesChannelId $salesChannelId
    );

    /**
     * Finds the specific product with the given Product ID and Sales Channel ID.
     *
     * @param ProductId      $productId
     * @param SalesChannelId $salesChannelId
     *
     * @return Product An implementation of Product
     */
    public function findSpecific(
        ProductId $productId,
        SalesChannelId $salesChannelId
    );
}
