<?php

namespace RetailExpress\SkyLink\Products;

use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader as XmlReader;
use Sabre\Xml\XmlDeserializable;

class V2ProductDeserializer implements XmlDeserializable
{
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        $pendingConfigurableProductState = 'none';
        if (isset($payload['MatrixProduct'])) {
            throw new \Exception("Find if MatrixProduct is a string or integer.");
            $pendingConfigurableProductState = $payload['MatrixProduct'] === 1 ? 'parent' : 'child';
        }

        return PendingProduct::fromNative(
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
            array_get_notempty($payload, 'ShippingCubic'),
            $pendingConfigurableProductState
        );
    }
}
