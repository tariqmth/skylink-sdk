<?php

namespace RetailExpress\SkyLink\Sdk\Apis\V2;

use Prezent\Soap\Client\SoapClient;
use Prezent\Soap\Client\Extension\WSAddressing;
use Sabre\Xml\Service as XmlService;
use SoapFault;
use SoapHeader;
use ValueObjects\Identity\UUID as Uuid;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\SchemeName;
use ValueObjects\Web\Url;

class Api
{
    private $soapClient;

    /**
     * @var Middleware[]
     */
    private $middleware = [];

    /**
     * @var callable
     */
    private $cachedMiddlewareChain;

    public function __construct(
        Url $url,
        Uuid $clientId,
        StringLiteral $username,
        StringLiteral $password,
        array $middleware = null
    ) {
        $this->assertSecureUrl($url);

        $this->soapClient = $this->createSoapClient($url, $clientId, $username, $password);

        if (null === $middleware) {
            $middleware = $this->createDefaultMiddleware();
        }

        $this->middleware = array_map(function (Middleware $middleware) {
            return $middleware;
        }, $middleware);
    }

    /**
     * Make a call to the V2 API.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function call($method, array $arguments = [])
    {
        try {
            $response = $this->soapClient->__soapCall($method, [$arguments]);
            $soapFault = null;
        } catch (SoapFault $soapFault) {
            $response = $this->soapClient->__getLastResponse();
        }

        return $this->getMiddlewareChain($this->middleware)(
            $this->soapClient->__getLastRequest(),
            $response,
            $soapFault
        );
    }

    /**
     * Adds a new Middleware into the stack.
     *
     * @param Middleware $middleware
     *
     * @return void
     */
    public function addMiddleware(Middleware $middleware)
    {
        $this->middleware[] = $middleware;
        $this->middlewareChain = null;
    }

    /**
     * Adds a new Middleware in a specific position in the stack.
     *
     * @param Middleware $middleware
     * @param string     $after      The instance name to insert the new middleware after
     *
     * @return void
     */
    public function addMiddlewareAfter(middleware $middleware, $after)
    {
        $classes = array_map(function (Middleware $middleware) {
            return get_class($middleware);
        }, $this->middleware);

        $index = array_search($after, $classes);

        if (false === $index) {
            throw new ApiException(sprintf(
                'Cannot add new Middleware after "%s" as it does not exist.',
                $after
            ));
        }

        array_splice($this->middleware, $index, 0, [$middleware]);
        $this->middlewareChain = null;
    }

    /**
     * Get the SOAP Client.
     *
     * @return SoapClient
     */
    public function getSoapClient()
    {
        return $this->soapClient;
    }

    /**
     * Create the default Middleware for the V2 API.
     *
     * @return Middleware[]
     */
    private function createDefaultMiddleware()
    {
        return [
            new NestedResponseExtractorMiddleware(),
            new InvalidClientIdMiddleware(),
            new EmptyResponseMiddleware(),
            new WebServicesExceptionMiddleware(),
            new SoapFaultMiddleware(),
            new GeneralErrorsMiddleware(),
        ];
    }

    /**
     * @param Middleware[] $middlewareList
     *
     * @return callable
     */
    private function getMiddlewareChain(array $middlewareList)
    {
        if (null === $this->cachedMiddlewareChain) {

            // The final callable returns the response
            $middlewareChain = function ($request, $response) {
                return $response;
            };

            while ($middleware = array_pop($middlewareList)) {
                $middlewareChain = function ($request, &$response, SoapFault $soapFault = null) use ($middleware, $middlewareChain) {
                    return $middleware->execute($request, $response, $soapFault, $middlewareChain);
                };
            }

            $this->cachedMiddlewareChain = $middlewareChain;
        }

        return $this->cachedMiddlewareChain;
    }

    public function getXmlService()
    {
        return new XmlService();
    }

    private function assertSecureUrl(Url $url)
    {
        if (!$url->getScheme()->sameValueAs(new SchemeName('https'))) {
            throw new ApiException("V2 API URL \"{$url}\" does not adhere to secure HTTPS mode.");
        }
    }

    private function createSoapClient(Url $url, Uuid $clientId, StringLiteral $username, StringLiteral $password)
    {
        $client = new SoapClient((string) $url, [
            'soap_version' => SOAP_1_2,
            'trace' => true,
            'event_subscribers' => [
                new WSAddressing(),
            ],
        ]);

        $header = new SoapHeader('http://retailexpress.com.au/', 'ClientHeader', [
            'ClientID' => (string) $clientId,
            'UserName' => (string) $username,
            'Password' => (string) $password,
        ]);

        $client->__setSoapHeaders($header);

        return $client;
    }
}
