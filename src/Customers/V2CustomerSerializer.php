<?php

namespace RetailExpress\SkyLink\Customers;

use Sabre\Xml\Writer as XmlWriter;

trait V2CustomerSerializer
{
    public function xmlSerialize(XmlWriter $xmlReader)
    {
        $payload = [];

        if ($this->id !== null) {
            $payload['CustomerId'] = $this->id->toInt();
        }

        if ($this->password !== null) {
            $payload['Password'] = $this->password;
        }

        $payload['BillFirstName'] = $this->billingAddress->getFirstName();
        $payload['BillLastName'] = $this->billingAddress->getLastName();
        $payload['BillEmail'] = $this->email->toString();

        /*
         * @todo Refactor getLines() into getLine1() and getLine2()
         */
        foreach ($this->billingAddress->getLines() as $index => $line) {
            $payload['BillAddress'.($index === 1 ? '2' : '')] = $line;
        }

        $payload['BillSuburb'] = $this->billingAddress->getSuburb();
        $payload['BillPostCode'] = $this->billingAddress->getPostcode();
        $payload['BillState'] = $this->billingAddress->getState();
        $payload['BillCountry'] = $this->billingAddress->getCountry();

        foreach ($this->billingAddress->getPhones() as $type => $number) {
            $payload['Bill'.ucfirst($type)] = $number;
        }

        $billingCompany = $this->billingAddress->getCompany();
        if ($billingCompany !== null) {
            $payload['ACN'] = $billingCompany->getAbn()->getNumber();
            $payload['BillCompany'] = $billingCompany->getName();

            if ($billingCompany->getWebsite() !== null) {
                $payload['BillWebsite'] = $billingCompany->getWebsite()->getUrl();
            }
        }

        foreach ($this->deliveryAddress->getLines() as $index => $line) {
            $payload['DelAddress'.($index === 1 ? '2' : '')] = $line;
        }

        $payload['DelSuburb'] = $this->deliveryAddress->getSuburb();
        $payload['DelPostCode'] = $this->deliveryAddress->getPostcode();
        $payload['DelState'] = $this->deliveryAddress->getState();
        $payload['DelCountry'] = $this->deliveryAddress->getCountry();

        foreach ($this->deliveryAddress->getPhones() as $type => $number) {
            $payload['Del'.ucfirst($type)] = $number;
        }

        $deliveryCompany = $this->deliveryAddress->getCompany();
        if ($deliveryCompany !== null) {
            $payload['DelCompany'] = $deliveryCompany->getName();
        }

        $payload['ReceivesNews'] = (int) $this->isSubsribedToNewsletter();

        // Filter out empty nodes
        $payload = array_filter($payload, function ($value) {
            return $value !== null;
        });

        $xmlReader->write($payload);
    }
}
