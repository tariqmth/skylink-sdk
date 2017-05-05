<?php

namespace RetailExpress\SkyLink\Sdk\Apis\V2;

use SoapFault;

interface Middleware
{
    /**
     * @param string    $request
     * @param string    $response
     * @param SoapFault $soapFault
     * @param callable  $next
     *
     * @return mixed
     */
    public function execute($request, &$response, SoapFault $soapFault = null, callable $next);
}
