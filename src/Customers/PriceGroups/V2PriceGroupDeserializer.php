<?php

namespace RetailExpress\SkyLink\Sdk\Customers\PriceGroups;

use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader as XmlReader;

trait V2PriceGroupDeserializer
{
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        return PriceGroup::fromNative(
            strtolower($payload['PriceGroupType']),
            $payload['ID'],
            $payload['Name']
        );
    }
}
