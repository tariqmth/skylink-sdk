<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeCode;

interface Product
{
    public function getId();

    public function getSku();

    public function getManufacturerSku();

    public function getName();

    public function getDescription();

    public function getPricingStructure();

    public function getInventoryItem();

    public function getPhysicalPackage();

    public function getAttributeOption(AttributeCode $attributeCode);

    public function getProductType();

    public function getMatrixProduct();

    public function setMatrixProduct(Matrix $matrixProduct);
}
