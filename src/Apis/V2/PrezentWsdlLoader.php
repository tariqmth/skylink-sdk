<?php

namespace RetailExpress\SkyLink\Sdk\Apis\V2;

use GuzzleHttp\Client;
use Prezent\Soap\Client\Event\WsdlRequestEvent;

/**
 * There is currently a bug with Prezent's WSDL loading functionality, whereby
 * it opens a new file handler for every WSDL Request Event, let's fix that.
 */
class PrezentWsdlLoader
{
    /**
     * @var Api
     */
    private $api;

    /**
     * @var Client
     */
    private $client;

    /**
     * Create a new WSDL Loader instance.
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * Handle the event.
     */
    public function handle(WsdlRequestEvent $event)
    {
        $body = $this->getClient()
            ->get($event->getUri())
            ->getBody();

        $event->setWsdl((string) $body);

        $event->stopPropagation();
    }

    /**
     * Gets the Guzzle Client instance.
     *
     * @return Client
     */
    private function getClient()
    {
        if (null === $this->client) {
            $this->client = $this->createClient();
        }

        return $this->client;
    }

    /**
     * Creates a Guzzle Client.
     *
     * @return Client
     */
    private function createClient()
    {
        return new Client();
    }
}
