<?php

namespace RetailExpress\SkyLink\Products;

use Sabre\Xml\XmlDeserializable;

class SimpleProduct extends Product implements XmlDeserializable
{
    public static function create(ProductId $id, $sku, $name, array $data = [])
    {
        return new self($id, $sku, $name, $data);
    }
}
