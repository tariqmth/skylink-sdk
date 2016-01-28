<?php

namespace RetailExpress\SkyLink\Customers;

use Sabre\Xml\XmlDeserializable;

class Customer implements XmlDeserializable
{
    use V2CustomerDeserializer;

    private $id;

    private $email;

    private $firstName;

    private $lastName;

    private $billingAddress;

    private $deliveryAddress;

    private $subscribedToNewsletter;

    private function __construct(
        CustomerId $id,
        Email $email,
        $firstName,
        $lastName,
        Address $billingAddress,
        Address $deliveryAddress,
        $subscribedToNewsletter
    )
    {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = (string) $firstName;
        $this->lastName = (string) $lastName;
        $this->billingAddress = $billingAddress;
        $this->deliveryAddress = $deliveryAddress;
        $this->subscribedToNewsletter = (bool) $subscribedToNewsletter;
    }

    public static function create(
        CustomerId $id,
        Email $email,
        $firstName,
        $lastName,
        Address $billingAddress,
        Address $deliveryAddress,
        $subscribedToNewsletter
    )
    {
        return new self(
            $id,
            $email,
            $firstName,
            $lastName,
            $billingAddress,
            $deliveryAddress,
            $subscribedToNewsletter
        );
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }
}
