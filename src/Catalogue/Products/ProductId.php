<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use ValueObjects\Number\Integer;
use Sabre\Xml\XmlDeserializable;

class ProductId extends Integer implements XmlDeserializable
{
    use V2ProductIdDeserializer;
}
