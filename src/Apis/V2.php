<?php

namespace RetailExpress\SkyLink\Apis;

use Ramsey\Uuid\Uuid;
use Sabre\Xml\Service as XmlService;
use SoapClient;
use SoapFault;
use SoapHeader;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\SchemeName;
use ValueObjects\Web\Url;

class V2
{
    public $soapClient;

    public static function fromNative($url, $clientId, $username, $password)
    {
        return new self(
            Url::fromNative($url),
            Uuid::fromString($clientId),
            new StringLiteral($username),
            new StringLiteral($password)
        );
    }

    public function __construct(Url $url, Uuid $clientId, StringLiteral $username, StringLiteral $password)
    {
        $this->assertSecureUrl($url);

        $this->soapClient = $this->createSoapClient(
            $url,
            $clientId,
            $username,
            $password
        );
    }

    public function call($method, array $arguments = [])
    {
        try {
            $response = $this->soapClient->__soapCall($method, [$arguments]);
        } catch (SoapFault $soapFault) {
            // To determine if the response is valid XML or not, we'll look for the presence of
            // the XML opening tag. If it does not exist, we know we are dealing with a zipped
            // response instead, at which point we'll write the response to a temporary file
            // and return a resource pointing to the temporary file so that it's contents
            // may be parsed without hindering on memory too badly.
            $response = $this->soapClient->__getLastResponse();
            if (starts_with($response, '<?xml version="1.0" encoding="utf-8"?>')) {
                $message = $this->extractUsefulSoapFaultMessage($soapFault->getMessage());

                $v2ApiException = new V2ApiException($message);
                $v2ApiException->setSoapFault($soapFault);

                throw $v2ApiException;
            }

            $response = $this->unzipReponse($response);
        }

        return $this->dissectApiResponse($response);
    }

    public function getXmlService()
    {
        return new XmlService();
    }

    private function unzipReponse($response)
    {
        // Write the response to a temporary file
        $zippedFile = $this->getTemporaryFileResource();
        fwrite($zippedFile, $response);

        // Grab the filename and close the handle
        $zippedFilename = stream_get_meta_data($zippedFile)['uri'];

        $response = '';

        // Open a new unzipped file handle based on the zipped file we just wrote
        $unzippedFile = gzopen($zippedFilename, 'r');

        while (!gzeof($unzippedFile)) {
            $response .= gzgetc($unzippedFile);
        }

        fclose($zippedFile);
        gzclose($unzippedFile);

        return $response;
    }

    private function getTemporaryFileResource()
    {
        return tmpfile();
    }

    /**
     * Retail Express returns API responses and errors in a few formats. This method is designed
     * to inspect responses for errors as well as return response paylaods rather than the junk
     * that wraps them.
     *
     * @param string|stdClass $response
     *
     * @return string
     *
     * @throws V2ApiException
     */
    private function dissectApiResponse($response)
    {
        if (is_object($response)) {
            $responseAsArray = get_object_vars($response);

            // If we ever encounter a situation where there's more than one element in our response
            // then we'll look into using array_first() with logic for finding the element that
            // we need. Until that time, we'll just throw an exception so we don't have any
            // strange behaviour leaking out and producing strange bugs.
            $responseElements = count($responseAsArray);
            if ($responseElements !== 1) {
                throw new V2ApiException("Expected 1 element in an API response, but received {$responseElements}.");
            }

            $payload = array_shift($responseAsArray)->any;
        } else {
            $payload = $response;
        }

        $this->checkPayloadForErrors($payload);

        return $payload;
    }

    private function checkPayloadForErrors($rawPayload)
    {
        // @todo Reimplement error handling
        return;

        $xmlService = $this->getXmlService();
        $xmlService->elementMap = [
            '{}Response' => 'Sabre\Xml\Deserializer\keyValue',
        ];
        $parsedPayload = $xmlService->parse($rawPayload);

        // Check for generic errors
        if (isset($parsedPayload['{}Error'])) {
            throw new V2ApiException($parsedPayload['{}Error']);
        }

        /*
         * Check for responses signalling validation failures (unfortuantely there are no messages).
         * Yes, this is a particularly strange response payload, SOAP API FTW.
         *
         * @link https://www.dropbox.com/s/r8hqgu3amjo4j6z/Screenshot%202016-02-23%2010.34.55.png?dl=0
         */
        array_walk($parsedPayload, function (&$value, $key) {
            foreach ($value as $childValue) {
                if (isset($childValue['name']) && $childValue['name'] === '{}Result') {
                    if ($childValue['value'] === 'Fail') {
                        throw new V2ApiException('Unspecified API failure.');
                    }
                }
            }
        });
    }

    private function assertSecureUrl(Url $url)
    {
        if (!$url->getScheme()->sameValueAs(new SchemeName('https'))) {
            throw new V2ApiException("V2 API URL \"{$url}\" does not adhere to secure HTTPS mode.");
        }
    }

    private function createSoapClient(Url $url, Uuid $clientId, StringLiteral $username, StringLiteral $password)
    {
        $client = new SoapClient((string) $url, [
            'soap_version' => SOAP_1_2,
            'trace' => true,
        ]);

        $header = new SoapHeader('http://retailexpress.com.au/', 'ClientHeader', [
            'ClientID' => (string) $clientId,
            'UserName' => (string) $username,
            'Password' => (string) $password,
        ]);

        $client->__setSoapHeaders($header);

        return $client;
    }

    private function extractUsefulSoapFaultMessage($message)
    {
        preg_match('/^System.Web.Services.Protocols.SoapException: (.*?)\n/', $message, $matches);

        return count($matches === 2) ? $matches[1] : $message;
    }
}
