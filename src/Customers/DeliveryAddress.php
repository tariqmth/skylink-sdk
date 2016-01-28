<?php

namespace RetailExpress\SkyLink\Customers;

use RetailExpress\SkyLink\ValueObject;
use InvalidArgumentException;

/**
 * @todo Should this be "delivery" as per Retail Express, or "shipping" as per Magento?
 */
class DeliveryAddress extends Address implements ValueObject
{

}
