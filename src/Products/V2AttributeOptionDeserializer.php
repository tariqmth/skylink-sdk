<?php

namespace RetailExpress\SkyLink\Products;

use InvalidArgumentException;
use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader as XmlReader;

trait V2AttributeOptionDeserializer
{
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        if (!array_key_exists('ProductId', $payload)) {
            return self::decodePredefinedAttribute($payload);
        }

        return self::decodeProductAttribute($payload);
    }

    private static function decodePredefinedAttribute(array $payload)
    {
        $attributeCodes = array_filter(AttributeCode::getConstants(), function ($value) {
            return !str_is('custom_*', $value);
        });

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

    private static function decodeProductAttribute(array $payload)
    {
        $attributeCodes = array_filter(AttributeCode::getConstants(), function ($value) {
            return str_is('custom_*', $value);
        });

        $options = [];

        foreach ($attributeCodes as $attributeCode) {
            $studlyAttributeCode = studly_case($attributeCode);

            if (!array_key_exists($studlyAttributeCode, $payload)) {
                continue;
            }

            $options[] = self::fromNative($attributeCode, $payload[$studlyAttributeCode]);
        }

        return $options;
    }
}
