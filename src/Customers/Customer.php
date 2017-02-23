<?php

namespace RetailExpress\SkyLink\Sdk\Customers;

use LogicException;
use Sabre\Xml\XmlDeserializable;
use Sabre\Xml\XmlSerializable;
use RetailExpress\SkyLink\Sdk\Customers\PriceGroups\PriceGroup;
use RetailExpress\SkyLink\Sdk\Customers\PriceGroups\PriceGroupKey;
use RetailExpress\SkyLink\Sdk\Customers\PriceGroups\PriceGroupType;
use ValueObjects\StringLiteral\StringLiteral;

class Customer implements XmlDeserializable, XmlSerializable
{
    use V2CustomerDeserializer;
    use V2CustomerSerializer;

    private $billingContact;

    private $shippingContact;

    private $newsletterSubscription;

    private $id;

    private $password;

    private $priceGroupKeys = [];

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
        StringLiteral $password,
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

    private function __construct(
        BillingContact $billingContact,
        ShippingContact $shippingContact,
        NewsletterSubscription $newsletterSubscription,
        CustomerId $id = null,
        StringLiteral $password = null
    ) {
        $this->billingContact = $billingContact;
        $this->shippingContact = $shippingContact;
        $this->newsletterSubscription = $newsletterSubscription;
        $this->id = $id;
        $this->password = $password;
    }

    /**
     * @see \RetailExpress\SkyLink\Sdk\Sales\Orders\Order::setId()
     */
    public function setId(CustomerId $id)
    {
        if (null !== $this->getId()) {
            throw new LogicException('Customer ID already set, cannot override.');
        }

        $this->id = $id;
    }

    public function withPriceGroupKey(PriceGroupKey $priceGroupKey)
    {
        $priceGroupType = (string) $priceGroupKey->getType();

        if (array_key_exists($priceGroupType, $this->priceGroupKeys)) {
            $message = "Only one price group of each type can be used, trying to assign \"{$priceGroupType}\" type twice.";
            throw new LogicException($message);
        }

        $new = clone $this;
        $new->priceGroupKeys[$priceGroupType] = $priceGroupKey;

        return $new;
    }

    public function hasPriceGroupKey(PriceGroupType $priceGroupType)
    {
        return array_key_exists((string) $priceGroupType, $this->priceGroupKeys);
    }

    public function getPriceGroupKey(PriceGroupType $priceGroupType)
    {
        if (!$this->hasPriceGroupKey($priceGroupType)) {
            return null;
        }

        return clone $this->priceGroupKeys[(string) $priceGroupType];
    }

    public function getPriceGroupKeys()
    {
        return array_values(array_map(function (PriceGroupKey $priceGroupKey) {
            return clone $priceGroupKey;
        }, $this->priceGroupKeys));
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
        if (null === $this->id) {
            return null;
        }

        return clone $this->id;
    }

    public function getPassword()
    {
        if (null === $this->password) {
            return null;
        }

        return clone $this->password;
    }
}
