<?php

namespace RetailExpress\SkyLink\Products;

use Sabre\Xml\XmlDeserializable;

class Product implements XmlDeserializable
{
    use V2ProductDeserializer;

    private $id;

    private $sku;

    private $name;

    private $data = [];

    public function __construct(ProductId $id, $sku, $name, array $data = [])
    {
        $this->id = $id;
        $this->sku = (string) $sku;
        $this->name = (string) $name;
        $this->data = $data;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getData()
    {
        return $this->data;
    }
}
