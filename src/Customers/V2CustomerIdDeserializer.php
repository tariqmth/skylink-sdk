<?php

namespace RetailExpress\SkyLink\Sdk\Customers;

use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader as XmlReader;

trait V2CustomerIdDeserializer
{
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        return new self($payload['CustomerId']);
    }
}
