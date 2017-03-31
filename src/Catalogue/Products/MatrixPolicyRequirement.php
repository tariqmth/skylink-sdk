<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use ValueObjects\Enum\Enum;

class MatrixPolicyRequirement extends Enum
{
    const ALL = 'all';
    const ANY = 'any';

    public static function getDefault()
    {
        return self::get('all');
    }

    public function isAll()
    {
        return self::ALL === $this->getValue();
    }

    public function isAny()
    {
        return self::ANY === $this->getValue();
    }
}
