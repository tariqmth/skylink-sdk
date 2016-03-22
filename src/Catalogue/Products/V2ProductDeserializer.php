<?php

namespace RetailExpress\SkyLink\Catalogue\Products;

use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader as XmlReader;

trait V2ProductDeserializer
{
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        return self::fromNative(
            $payload['ProductId'],
            $payload['SKU'],
            $payload['Description'],
            $payload['DefaultPrice'],
            $payload['DiscountedPrice'],
            $payload['ManageStock'],
            $payload['StockAvailable'],
            array_get_notempty($payload, 'Weight', 0),
            array_get_notempty($payload, 'Length', 0),
            array_get_notempty($payload, 'Breadth', 0),
            array_get_notempty($payload, 'Depth', 0),
            array_get_notempty($payload, 'ShippingCubic')
        );
    }
}
