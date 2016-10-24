<?php

namespace spec\RetailExpress\SkyLink\Sdk\ValueObjects;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use InvalidArgumentException;
use RetailExpress\SkyLink\Sdk\ValueObjects\TaxRate;
use ValueObjects\NullValue\NullValue;

class TaxRateSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedThrough('fromNative', [0.1]);
        $this->shouldHaveType(TaxRate::class);
    }

    function it_asserts_a_valid_rate()
    {
        $this->beConstructedThrough('fromNative', ['1.1']);
        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }

    function it_returns_rate()
    {
        $this->beConstructedThrough('fromNative', [0.1]);
        $this->getRate()->toNative()->shouldBe(0.1);
    }

    function it_is_initializable_with_percentage()
    {
        $this->beConstructedThrough('fromNative', ['10%']);
        $this->getRate()->toNative()->shouldBe(0.1);
    }

    function it_knows_if_the_rate_is_taxable()
    {
        $this->beConstructedThrough('fromNative', [0.1]);
        $this->isTaxable()->shouldBe(true);
    }

    function it_knows_if_the_rate_is_not_taxable()
    {
        $this->beConstructedThrough('fromNative', [0]);
        $this->isTaxable()->shouldBe(false);
    }

    function its_value_can_be_compared_against_an_equal()
    {
        $this->beConstructedThrough('fromNative', [0.1]);
        $this->sameValueAs(TaxRate::fromNative(0.1))->shouldBe(true);
    }

    function its_value_can_be_compared_against_a_non_equal_of_the_same_type()
    {
        $this->beConstructedThrough('fromNative', [0.1]);
        $this->sameValueAs(TaxRate::fromNative(0))->shouldBe(false);
    }

    function its_value_can_be_compared_against_a_non_equal_of_different_type()
    {
        $this->beConstructedThrough('fromNative', [0.1]);
        $this->sameValueAs(new NullValue())->shouldBe(false);
    }

    function it_can_be_represented_as_a_string()
    {
        $this->beConstructedThrough('fromNative', [0.1]);
        $this->__toString()->shouldBe('0.1');
    }
}
