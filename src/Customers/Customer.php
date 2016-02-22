<?php

namespace RetailExpress\SkyLink\Customers;

use Sabre\Xml\XmlDeserializable;
use Sabre\Xml\XmlSerializable;

class Customer implements XmlDeserializable, XmlSerializable
{
    use V2CustomerDeserializer;
    use V2CustomerSerializer;

    private $email;

    private $billingAddress;

    private $deliveryAddress;

    private $subscribedToNewsletter;

    private $id;

    private $password;

    /**
     * @todo Investigate ordering, currently only delivery address is required with API
     */
    private function __construct(
        Email $email,
        Address $billingAddress,
        Address $deliveryAddress,
        $subscribedToNewsletter,
        CustomerId $id = null,
        $password = null
    ) {
        $this->email = $email;
        $this->billingAddress = $billingAddress;
        $this->deliveryAddress = $deliveryAddress;
        $this->subscribedToNewsletter = (bool) $subscribedToNewsletter;
        $this->id = $id;
        $this->password = isset($password) ? trim((string) $password) : null;
    }

    public static function existing(
        CustomerId $id,
        Email $email,
        Address $billingAddress,
        Address $deliveryAddress,
        $subscribedToNewsletter
    ) {
        return new self(
            $email,
            $billingAddress,
            $deliveryAddress,
            $subscribedToNewsletter,
            $id
        );
    }

    public static function register(
        Email $email,
        $password,
        Address $billingAddress,
        Address $deliveryAddress,
        $subscribedToNewsletter
    ) {
        return new self(
            $email,
            $billingAddress,
            $deliveryAddress,
            $subscribedToNewsletter,
            null,
            $password
        );
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    public function getDeliveryAddress()
    {
        return $this->deliveryAddress;
    }

    public function isSubsribedToNewsletter()
    {
        return $this->subscribedToNewsletter;
    }
}
