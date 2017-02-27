<?php

namespace RetailExpress\SkyLink\Sdk\Loyalty;

use Sabre\Xml\XmlDeserializable;
use ValueObjects\Number\Natural;

class Loyalty extends Natural implements XmlDeserializable
{
    use V2LoyaltyDeserializer;
}
