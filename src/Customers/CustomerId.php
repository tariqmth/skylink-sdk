<?php

namespace RetailExpress\SkyLink\Sdk\Customers;

use ValueObjects\Number\Integer;
use Sabre\Xml\XmlDeserializable;

class CustomerId extends Integer implements XmlDeserializable
{
    use V2CustomerIdDeserializer;
}
