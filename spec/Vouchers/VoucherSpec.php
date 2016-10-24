<?php

namespace spec\RetailExpress\SkyLink\Sdk\Vouchers;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use BadMethodCallException;
use RetailExpress\SkyLink\Sdk\Vouchers\Voucher;

class VoucherSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedThrough('fromNative', ['voucher code', 100]);
        $this->shouldHaveType(Voucher::class);
    }

    function it_checks_the_number_of_arguments()
    {
        $this->beConstructedThrough('fromNative', []);
        $this->shouldThrow(BadMethodCallException::class)->duringInstantiation();
    }

    function it_should_return_the_code()
    {
        $this->beConstructedThrough('fromNative', ['voucher code', 100]);
        $this->getCode()->toNative()->shouldBe('voucher code');
    }

    function it_should_return_the_balance()
    {
        $this->beConstructedThrough('fromNative', ['voucher code', 100]);
        $this->getBalance()->toNative()->shouldBe(100.0);
    }
}
