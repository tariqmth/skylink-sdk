<?php

namespace RetailExpress\SkyLink\Customers;

use Sabre\Xml\XmlDeserializable;
use Sabre\Xml\XmlSerializable;
use ValueObjects\Web\EmailAddress;

class Customer implements XmlDeserializable, XmlSerializable
{
    use V2CustomerDeserializer;
    use V2CustomerSerializer;

    private $emailAddress;

    private $billingAddress;

    private $deliveryAddress;

    private $subscribedToNewsletter;

    private $id;

    private $password;

    private function __construct(
        EmailAddress $emailAddress,
        Address $billingAddress,
        Address $deliveryAddress,
        $subscribedToNewsletter,
        CustomerId $id = null,
        $password = null
    ) {
        $this->emailAddress = $emailAddress;
        $this->billingAddress = $billingAddress;
        $this->deliveryAddress = $deliveryAddress;
        $this->subscribedToNewsletter = (bool) $subscribedToNewsletter;
        $this->id = $id;
        $this->password = isset($password) ? trim((string) $password) : null;
    }

    public static function existing(
        CustomerId $id,
        EmailAddress $emailAddress,
        Address $billingAddress,
        Address $deliveryAddress,
        $subscribedToNewsletter
    ) {
        return new self(
            $emailAddress,
            $billingAddress,
            $deliveryAddress,
            $subscribedToNewsletter,
            $id
        );
    }

    public static function register(
        EmailAddress $emailAddress,
        $password,
        Address $billingAddress,
        Address $deliveryAddress,
        $subscribedToNewsletter
    ) {
        return new self(
            $emailAddress,
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

    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    public function getShippingAddress()
    {
        return $this->deliveryAddress;
    }

    public function isSubsribedToNewsletter()
    {
        return $this->subscribedToNewsletter;
    }
}
