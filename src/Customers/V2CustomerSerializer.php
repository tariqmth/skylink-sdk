<?php

namespace RetailExpress\SkyLink\Sdk\Customers;

use Sabre\Xml\Writer as XmlWriter;

trait V2CustomerSerializer
{
    use V2BillingAndShippingContactSerializer;

    public function xmlSerialize(XmlWriter $xmlWriter)
    {
        $payload = [];

        if (null !== $this->id) {
            $payload['CustomerId'] = $this->id->toInteger();
        }

        if (null !== $this->getPassword()) {
            $payload['Password'] = (string) $this->getPassword();
        }

        $payload = array_merge(
            $payload,
            $this->serializeBillingAndShippingContacts(
                $this->getBillingContact(),
                $this->getShippingContact()
            )
        );

        $payload['ReceivesNews'] = (string) $this->getNewsletterSubscription();

        $xmlWriter->write($payload);
    }
}
