<?php

namespace RetailExpress\SkyLink\Sdk\Apis\V2;

use SoapFault;

class NestedResponseExtractorMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function execute($request, &$response, SoapFault $soapFault = null, callable $next)
    {
        if (is_object($response)) {
            $responseAsArray = get_object_vars($response);

            // If we ever encounter a situation where there's more than one element in our response
            // then we'll look into using array_first() with logic for finding the element that
            // we need. Until that time, we'll just throw an exception so we don't have any
            // strange behaviour leaking out and producing strange bugs.
            $responseElements = count($responseAsArray);
            if ($responseElements !== 1) {
                throw new ApiException("Expected 1 element in an API response, but received {$responseElements}.");
            }

            $response = array_shift($responseAsArray)->any;
        }

        return $next($request, $response, $soapFault);
    }
}
