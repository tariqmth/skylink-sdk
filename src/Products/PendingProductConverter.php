<?php

namespace RetailExpress\SkyLink\Products;

use BadMethodCallException;
use InvalidArgumentException;

class PendingProductConverter
{
    public function convert(array $pendingProducts)
    {
        // The first business rule here is that, if only one product is present,
        // then is must not be part of a configurable product. Let's check that
        // out
        if (count($pendingProducts) === 1) {
            $configurableState = $pendingProducts[0]->getPendingConfigurableProductState();

            if (!$configurableState->sameValueAs(PendingConfigurableProductState::fromNative('none'))) {
                throw new InvalidArgumentException("Only one product was returned from the API and was expected to be a single, simple product, however {$pendingProducts[0]->getSku()} did not conform to this rule.");
            }

            return SimpleProduct::fromPendingProduct($pendingProducts[0]);
        }

        throw new BadMethodCallException('Finish implementing '.__METHOD__);
    }
}
