<?php

namespace RetailExpress\SkyLink\Products;

use InvalidArgumentException;
use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader as XmlReader;

trait V2AttributeValueDeserializer
{
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        $attributeCodes = AttributeCode::getConstants();

        foreach ($attributeCodes as $attributeCode) {
            $studlyAttributeCode = studly_case($attributeCode);

            $valueIdKey = "{$studlyAttributeCode}Id";
            $labelKey = "{$studlyAttributeCode}Name";

            if (!array_key_exists($valueIdKey, $payload)) {
                continue;
            }

            return self::fromNative(
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
