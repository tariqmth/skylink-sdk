<?php

use RetailExpress\SkyLink\Catalogue\Attributes\AttributeCode;

interface Product
{
    public function getId();

    public function getSku();

    public function getName();

    public function getPricingStructure();

    public function getInventoryItem();

    public function getPhysicalPackage();

    public function getAttributeOption(AttributeCode $attributeCode);

    public function getProductType();
}
