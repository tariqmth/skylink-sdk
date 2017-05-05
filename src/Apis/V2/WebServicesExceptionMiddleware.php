<?php

namespace RetailExpress\SkyLink\Sdk\Apis\V2;

use SoapFault;

class WebServicesExceptionMiddleware implements Middleware
{
    const EXCEPTION_REGEX = '/^System.Web.Services.Protocols.SoapException: (.*?)\n/';

    /**
     * {@inheritdoc}
     */
    public function execute($request, &$response, SoapFault $soapFault = null, callable $next)
    {
        if (null !== $soapFault) {
            if (preg_match(self::EXCEPTION_REGEX, $soapFault->getMessage(), $matches)) {
                $message = sprintf(
                    'The API is reporting an Exception: "%s"',
                    count($matches === 2) ? $matches[1] : $message
                );

                throw ApiException::withSoapFaultAndRequest($soapFault, $request, $message);
            }
        }

        return $next($request, $response, $soapFault);
    }
}
