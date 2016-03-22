<?php

namespace RetailExpress\SkyLink\Catalogue\Attributes;

use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader as XmlReader;
use Sabre\Xml\XmlDeserializable;

class V2AdhocAttributeOptionDeserializer implements XmlDeserializable
{
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        $attributeCodes = AttributeCode::getAdhoc();

        $options = [];

        foreach ($attributeCodes as $attributeCode) {
            $studlyAttributeCode = studly_case($attributeCode);

            if (!array_key_exists($studlyAttributeCode, $payload)) {
                continue;
            }

            $options[] = AttributeOption::fromNative(
                $attributeCode,
                $payload[$studlyAttributeCode],
                $payload[$studlyAttributeCode]
            );
        }

        return $options;
    }
}
