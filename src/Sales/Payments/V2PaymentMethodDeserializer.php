<?php

namespace RetailExpress\SkyLink\Sales\Payments;

use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader as XmlReader;

trait V2PaymentMethodDeserializer
{
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        return self::fromNative(
            $payload['ID'],
            $payload['Name'],
            $payload['Enabled'] === 'true'
        );
    }
}
