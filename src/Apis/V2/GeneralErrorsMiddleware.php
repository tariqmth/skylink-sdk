<?php

namespace RetailExpress\SkyLink\Sdk\Apis\V2;

use SoapFault;

class GeneralErrorsMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function execute($request, &$response, SoapFault $soapFault = null, callable $next)
    {
        // Todo, tidy this up, maybe globalise it?
        $xml = simplexml_load_string($response);
        $errors = array_map('trim', $xml->xpath('//Error'));

        if (count($errors) > 0) {
            throw new V2ApiException(sprintf(
                'The API reported the errors: "%s"',
                implode(' ', $errors)
            ));
        }

        return $next($request, $response, $soapFault);
    }
}
