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
}
