<?php

namespace spec\RetailExpress\SkyLink\Sdk\Sales\Orders;

use PhpSpec\ObjectBehavior;
use RetailExpress\SkyLink\Sdk\Sales\Orders\Order;

class OrderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Order::class);
    }
}
