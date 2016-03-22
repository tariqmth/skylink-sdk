<?php

namespace RetailExpress\SkyLink\Catalogue\Attributes;

use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader as XmlReader;
use Sabre\Xml\XmlDeserializable;

class V2PredefinedAttributeOptionDeserializer implements XmlDeserializable
{
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        $attributeCodes = AttributeCode::getPredefined();

        foreach ($attributeCodes as $attributeCode) {
            $studlyAttributeCode = studly_case($attributeCode);

            $valueIdKey = "{$studlyAttributeCode}Id";
            $labelKey = "{$studlyAttributeCode}Name";

            if (!array_key_exists($valueIdKey, $payload)) {
                continue;
            }

            return AttributeOption::fromNative(
                $attributeCode,
                $payload[$valueIdKey],
                $payload[$labelKey]
            );
        }

        throw new InvalidArgumentException(sprintf(
            'Found attribute in payload that did not match available defined attributes: %s.',
            implode(', ', $attributeCodes)
        ));
    }
}
