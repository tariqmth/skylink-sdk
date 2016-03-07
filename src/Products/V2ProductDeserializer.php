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

        $inventoryItem = InventoryItem::fromNative($payload['ManageStock'], $payload['StockOnHand']);

        $product = Product::fromNative(
            $payload['ProductId'],
            $payload['SKU'],
            $payload['Description'],
            $payload['ManageStock'],
            $payload['StockAvailable']
        );

        dd($payload, $product);

        // Pass the payload to a function to determine the product type:
        // 1. SimpleProduct
        // 2. ConfigurableProduct
        // 3. DownloadableProduct (need information from Retail Express)
        // 4. VirtualProduct      ( ^ ditto )
    }
}
