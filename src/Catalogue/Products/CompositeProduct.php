<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

/**
 * Class that is a flag for all product implementations that are treated as compsite products.
 */
interface CompositeProduct
{
    /**
     * Return all products associated with the composite product.
     *
     * @return Product[]
     */
    public function getProducts();
}
