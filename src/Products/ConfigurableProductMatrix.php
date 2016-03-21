<?php

namespace RetailExpress\SkyLink\Products;

class ConfigurableProductMatrix
{
    private $policy;

    private $products = [];

    public function __construct(ConfigurableProductPolicy $policy, array $products)
    {
        $this->policy = $policy;

        foreach ($products as $product) {
            $this->registerProduct($product);
        }
    }

    private function registerProduct(Product $product)
    {
        $this->products[] = $product;
    }

    public function getPolicy()
    {
        return clone $this->policy;
    }

    public function getProducts()
    {
        return clone $this->products;
    }
}
