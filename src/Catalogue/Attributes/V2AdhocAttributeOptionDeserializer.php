<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Attributes;

use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\ParseException;
use Sabre\Xml\Reader as XmlReader;
use Sabre\Xml\XmlDeserializable;

class V2AdhocAttributeOptionDeserializer implements XmlDeserializable
{
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $attributeCode = self::getAttributeCode($xmlReader->localName);
        $attributeValue = XmlDeserializer\keyValue($xmlReader, '')['Value'];

        return AttributeOption::fromNative(
            $attributeCode,
            $attributeValue,
            $attributeValue
        );
    }

    private static function getAttributeCode($nodeName)
    {
        return sprintf('custom_%s', substr($nodeName, -1));
    }
}
