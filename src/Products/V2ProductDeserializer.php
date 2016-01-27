<?php

namespace RetailExpress\SkyLink\Products;

use RetailExpress\SkyLink\XmlKeySanitiser;
use Sabre\Xml\Element\KeyValue as KeyValueElement;
use Sabre\Xml\Reader as XmlReader;

trait V2ProductDeserializer
{
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = KeyValueElement::xmlDeserialize($xmlReader);

        dd(XmlKeySanitiser::sanitise($payload));

        $product = static::create(
            new ProductId($payload['{}ProductId']),
            $payload['{}SKU'],
            $payload['{}Description'],
            array_except($payload, ['{}ProductId', '{}SKU', '{}Description'])
        );

        return $product;
    }
}
