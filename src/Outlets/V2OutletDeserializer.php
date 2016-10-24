<?php

namespace RetailExpress\SkyLink\Sdk\Outlets;

use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader as XmlReader;

trait V2OutletDeserializer
{
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        return self::fromNative(
            $payload['OutletId'],
            $payload['OutletName'],
            array_get_notempty($payload, 'Address1', ''),
            array_get_notempty($payload, 'Address2', ''),
            array_get_notempty($payload, 'Address3', ''),
            array_get_notempty($payload, 'Suburb', ''),
            array_get_notempty($payload, 'State', ''),
            array_get_notempty($payload, 'Postcode', ''),
            array_get_notempty($payload, 'Country', ''),
            array_get_notempty($payload, 'Phone', ''),
            array_get_notempty($payload, 'Fax', '')
        );
    }
}
