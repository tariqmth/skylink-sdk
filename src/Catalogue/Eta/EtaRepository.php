<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Eta;

use RetailExpress\SkyLink\Sdk\Catalogue\Products\ProductId;
use RetailExpress\SkyLink\Sdk\ValueObjects\SalesChannelId;

interface EtaRepository
{
    /**
     * Finds the ETA for the given product
     *
     * @param ProductId      $productId
     * @param EtaQty         $qty
     * @param SalesChannelId $salesChannelId
     *
     * @return ETA A value object representing an ETA
     */
    public function find(ProductId $productId, EtaQty $qty, SalesChannelId $salesChannelId);
}
