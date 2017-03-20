<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use League\CommonMark\CommonMarkConverter;
use ValueObjects\StringLiteral\StringLiteral;

class Description extends StringLiteral
{
    private static $converter;

    private $htmlValue;

    public function toHtml()
    {
        if (null === $this->htmlValue) {
            $this->htmlValue = self::getConverter()->convertToHtml($this->toNative());
        }

        return $this->htmlValue;
    }

    private static function getConverter()
    {
        if (null === self::$converter) {
            self::$converter = new CommonMarkConverter();
        }

        return self::$converter;
    }
}
