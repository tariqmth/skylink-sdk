<?php

namespace RetailExpress\SkyLink\Catalogue\Products;

class Matrix
{
    private $policy;

    private $products = [];

    public function __construct(MatrixPolicy $policy, array $products)
    {
        $this->policy = $policy;

        $this->products = array_map(function (Product $product) {
            return $product;
        }, $products);
    }

    public function getPolicy()
    {
        return clone $this->policy;
    }

    public function getProducts()
    {
        return array_map(function (Product $product) {
            return clone $product;
        }, $this->products);
    }
}