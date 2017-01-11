<?php

namespace RetailExpress\SkyLink\Sdk\Apis;

use DateTimeImmutable;
use Sabre\Xml\Service as XmlService;
use SoapClient;
use SoapFault;
use SoapHeader;
use ValueObjects\Identity\UUID as Uuid;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\SchemeName;
use ValueObjects\Web\Url;

class V2
{
    private $soapClient;

    public static function fromNative($url, $clientId, $username, $password)
    {
        return new self(
            Url::fromNative($url),
            Uuid::fromNative($clientId),
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

    public function call($method, array $arguments = [], callable $postProcessing = null)
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

            // @todo Look at __getLastResponseHeaders for "Content-Type: binary/x-gzip"
            if (starts_with($response, '<?xml version="1.0" encoding="utf-8"?>')) {
                $message = $this->extractUsefulSoapFaultMessage($soapFault->getMessage());
                throw V2ApiException::withSoapFault($soapFault, $message);
            }

            $response = $this->unzipReponse($response);
        }

        $response = $this->dissectApiResponse($response);

        if (null !== $postProcessing) {
            $postProcessing($response);
        }

        return $response;
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
        if (!is_object($response)) {
            return $response;
        }

        $responseAsArray = get_object_vars($response);

        // If we ever encounter a situation where there's more than one element in our response
        // then we'll look into using array_first() with logic for finding the element that
        // we need. Until that time, we'll just throw an exception so we don't have any
        // strange behaviour leaking out and producing strange bugs.
        $responseElements = count($responseAsArray);
        if ($responseElements !== 1) {
            throw new V2ApiException("Expected 1 element in an API response, but received {$responseElements}.");
        }

        return array_shift($responseAsArray)->any;
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
