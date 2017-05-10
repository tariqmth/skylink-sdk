<?php

namespace spec\RetailExpress\SkyLink\Sdk\Catalogue\Products;

use BadMethodCallException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RetailExpress\SkyLink\Sdk\Catalogue\Products\OutletQty;

class OutletQtySpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedThrough('fromNative', [1, 10]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(OutletQty::class);
    }

    public function it_can_be_cast_as_a_string()
    {
        $this->__toString()->shouldBe('1:10');
    }

    public function it_should_require_you_provide_two_arguments()
    {
        $this->beConstructedThrough('fromNative', [1]);

        $this->shouldThrow(BadMethodCallException::class)->duringInstantiation();
    }
}
