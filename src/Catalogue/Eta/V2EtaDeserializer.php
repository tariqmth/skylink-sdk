<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Eta;

use RetailExpress\SkyLink\Sdk\Catalogue\Products\ProductId;
use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader as XmlReader;

trait V2EtaDeserializer
{
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        if (!array_key_exists('ETA', $payload)) {
            return null;
        }

        return self::fromNative(
            $payload['ProductID'],
            $payload['QtyOrdered'],
            from_v2_rex_date_to_timestamp($payload['ETA'])
        );
    }
}
