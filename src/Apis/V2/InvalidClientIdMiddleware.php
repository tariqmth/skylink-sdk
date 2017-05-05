<?php

namespace RetailExpress\SkyLink\Sdk\Apis\V2;

use SoapFault;

class InvalidClientIdMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function execute($request, &$response, SoapFault $soapFault = null, callable $next)
    {
        if (str_contains($response, 'The client ID provided is not valid')) {
            throw new ApiException('The API is reporting that your Client ID is not valid.');
        }

        return $next($request, $response, $soapFault);
    }
}
