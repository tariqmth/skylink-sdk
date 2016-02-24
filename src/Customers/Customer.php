<?php

namespace RetailExpress\SkyLink\Customers;

use Sabre\Xml\XmlDeserializable;
use Sabre\Xml\XmlSerializable;

class Customer implements XmlDeserializable, XmlSerializable
{
    use V2CustomerDeserializer;
    use V2CustomerSerializer;

    private $billingContact;

    private $shippingContact;

    private $newsletterSubscription;

    private $id;

    private $password;

    private function __construct(
        BillingContact $billingContact,
        ShippingContact $shippingContact,
        NewsletterSubscription $newsletterSubscription,
        CustomerId $id = null,
        $password = null
    ) {
        $this->billingContact = $billingContact;
        $this->shippingContact = $shippingContact;
        $this->newsletterSubscription = $newsletterSubscription;
        $this->id = $id;
        $this->password = isset($password) ? trim((string) $password) : null;
    }

    public static function existing(
        CustomerId $id,
        BillingContact $billingContact,
        ShippingContact $shippingContact,
        NewsletterSubscription $newsletterSubscription
    ) {
        return new self(
            $billingContact,
            $shippingContact,
            $newsletterSubscription,
            $id
        );
    }

    public static function register(
        $password,
        BillingContact $billingContact,
        ShippingContact $shippingContact,
        NewsletterSubscription $newsletterSubscription
    ) {
        return new self(
            $billingContact,
            $shippingContact,
            $newsletterSubscription,
            null,
            $password
        );
    }

    public function getBillingContact()
    {
        return clone $this->billingContact;
    }

    public function getShippingContact()
    {
        return clone $this->shippingContact;
    }

    public function getNewsletterSubscription()
    {
        return clone $this->newsletterSubscription;
    }

    public function getId()
    {
        return clone $this->id;
    }

    public function getPassword()
    {
        return clone $this->password;
    }
}
