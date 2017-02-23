<?php

namespace RetailExpress\SkyLink\Sdk\Customers\PriceGroups;

interface PriceGroupRepository
{
    /**
     * Get all price groups.
     *
     * @return PriceGroup[]
     */
    public function all();

    /**
     * Get a specific price group.
     *
     * @param PriceGroupKey $priceGroupKey
     *
     * @return PriceGroup|null
     */
    public function find(PriceGroupKey $priceGroupKey);
}
