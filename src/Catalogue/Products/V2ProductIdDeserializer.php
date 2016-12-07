<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader as XmlReader;

trait V2ProductIdDeserializer
{
    /**
     * @todo Validate options from Attribute Repository
     */
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        return new self($payload['ProductId']);
    }
}
