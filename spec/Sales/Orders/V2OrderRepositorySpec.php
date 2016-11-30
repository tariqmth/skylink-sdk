<?php

namespace spec\RetailExpress\SkyLink\Sdk\Sales\Orders;

use BadMethodCallException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RetailExpress\SkyLink\Sdk\Apis\V2 as V2Api;
use RetailExpress\SkyLink\Sdk\Sales\Orders\OrderId;
use RetailExpress\SkyLink\Sdk\Sales\Orders\V2OrderRepository;

class V2OrderRepositorySpec extends ObjectBehavior
{
    private $v2Api;

    public function let(V2Api $v2Api)
    {
        $this->v2Api = $v2Api;

        $this->beConstructedWith($this->v2Api);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(V2OrderRepository::class);
    }

    public function it_does_no_support_getting_a_single_order()
    {
        $this->shouldThrow(BadMethodCallException::class)
            ->duringGet(new OrderId('1'));
    }
}