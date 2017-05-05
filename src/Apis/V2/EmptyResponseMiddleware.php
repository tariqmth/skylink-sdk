<?php

namespace RetailExpress\SkyLink\Sdk\Apis\V2;

use SoapFault;

class EmptyResponseMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function execute($request, &$response, SoapFault $soapFault = null, callable $next)
    {
        if (!$response) {
            throw new ApiException('An empty response was returned from the API. Check all of your credentials are correct perhaps?');
        }

        return $next($request, $response, $soapFault);
    }
}
