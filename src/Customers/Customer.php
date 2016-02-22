<?php

namespace RetailExpress\SkyLink\Customers;

use Sabre\Xml\XmlDeserializable;

class Customer implements XmlDeserializable
{
    use V2CustomerDeserializer;

    private $email;

    private $firstName;

    private $lastName;

    private $billingAddress;

    private $deliveryAddress;

    private $subscribedToNewsletter;

    private $id;

    private $password;

    private function __construct(
        Email $email,
        $firstName,
        $lastName,
        Address $billingAddress,
        Address $deliveryAddress,
        $subscribedToNewsletter,
        CustomerId $id = null,
        $password = null
    ) {
        $this->email = $email;
        $this->firstName = trim((string) $firstName);
        $this->lastName = trim((string) $lastName);
        $this->billingAddress = $billingAddress;
        $this->deliveryAddress = $deliveryAddress;
        $this->subscribedToNewsletter = (bool) $subscribedToNewsletter;
        $this->id = $id;
        $this->password = isset($password) ? trim((string) $password) : null;
    }

    public static function existing(
        CustomerId $id,
        Email $email,
        $firstName,
        $lastName,
        Address $billingAddress,
        Address $deliveryAddress,
        $subscribedToNewsletter
    ) {
        return new self(
            $email,
            $firstName,
            $lastName,
            $billingAddress,
            $deliveryAddress,
            $subscribedToNewsletter,
            $id
        );
    }

    public static function register(
        Email $email,
        $password,
        $firstName,
        $lastName,
        Address $billingAddress,
        Address $deliveryAddress,
        $subscribedToNewsletter
    ) {
        return new self(
            $email,
            $firstName,
            $lastName,
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

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
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
