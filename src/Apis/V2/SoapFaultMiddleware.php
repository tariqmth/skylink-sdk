<?php

namespace RetailExpress\SkyLink\Sdk\Apis\V2;

use SoapFault;

class SoapFaultMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function execute($request, &$response, SoapFault $soapFault = null, callable $next)
    {
        if (null !== $soapFault) {
            throw ApiException::withSoapFaultAndRequest($soapFault, $request);
        }

        return $next($request, $response, $soapFault);
    }
}
