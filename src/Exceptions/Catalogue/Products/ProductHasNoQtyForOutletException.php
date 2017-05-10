<?php

namespace RetailExpress\SkyLink\Sdk\Exceptions\Catalogue\Products;

use InvalidArgumentException;
use RetailExpress\SkyLink\Sdk\Catalogue\Products\ProductId;
use RetailExpress\SkyLink\Sdk\Outlets\OutletId;

class ProductHasNoQtyForOutletException extends InvalidArgumentException
{
    public static function withProductIdAndOutletId(
        ProductId $productId,
        OutletId $outletId
    ) {
        return new self(sprintf('Product #%s does not have a quantity specified for Outlet #%s', $productId, $outletId));
    }
}
