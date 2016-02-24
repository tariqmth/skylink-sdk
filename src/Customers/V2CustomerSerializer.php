<?php

namespace RetailExpress\SkyLink\Customers;

use Sabre\Xml\Writer as XmlWriter;

trait V2CustomerSerializer
{
    public function xmlSerialize(XmlWriter $xmlReader)
    {
        $payload = [];

        if (null !== $this->id) {
            $payload['CustomerId'] = $this->id->toInteger();
        }

        if (null !== $this->getPassword()) {
            $payload['Password'] = (string) $this->getPassword();
        }

        $payload['BillFirstName'] = (string) $this->getBillingContact()->getName()->getFirstName();
        $payload['BillLastName'] = (string) $this->getBillingContact()->getName()->getLastName();

        $payload['BillAddress'] = (string) $this->getBillingContact()->getAddress()->getLine1();
        $payload['BillAddress2'] = (string) $this->getBillingContact()->getAddress()->getLine2();
        $payload['BillCompany'] = (string) $this->getBillingContact()->getCompanyName();
        $payload['BillSuburb'] = (string) $this->getBillingContact()->getAddress()->getCity();
        $payload['BillPostCode'] = (string) $this->getBillingContact()->getAddress()->getPostcode();
        $payload['BillState'] = (string) $this->getBillingContact()->getAddress()->getState();
        $payload['BillCountry'] = (string) $this->getBillingContact()->getAddress()->getCountry();

        $payload['BillEmail'] = (string) $this->getBillingContact()->getEmailAddress();

        $payload['DelAddress'] = (string) $this->getShippingContact()->getAddress()->getLine1();
        $payload['DelAddress2'] = (string) $this->getShippingContact()->getAddress()->getLine2();
        $payload['DelSuburb'] = (string) $this->getShippingContact()->getAddress()->getCity();
        $payload['DelPostCode'] = (string) $this->getShippingContact()->getAddress()->getPostcode();
        $payload['DelState'] = (string) $this->getShippingContact()->getAddress()->getState();
        $payload['DelCountry'] = (string) $this->getShippingContact()->getAddress()->getCountry();

        $payload['ReceivesNews'] = (string) $this->getNewsletterSubscription();

        $xmlReader->write($payload);
    }
}
