<?php

namespace RetailExpress\SkyLink\Sdk\Loyalty;

use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader as XmlReader;

trait V2LoyaltyDeserializer
{
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        return new self($payload['LoyaltyPointsAvailable']);
    }
}

