<?php

namespace RetailExpress\SkyLink\Products;

use Sabre\Xml\XmlDeserializable;

class ConfigurableProduct extends Product implements XmlDeserializable
{
    protected $childProducts = [];

    public static function create(ProductId $id, $sku, $name, array $data = [], array $childProducts = [])
    {
        $product = new self($id, $sku, $name, $data);
        $product->childProducts = $childProducts;

        return $product;
    }
}
