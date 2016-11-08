<?php

namespace spec\RetailExpress\SkyLink\Sdk\ValueObjects;

use PhpSpec\ObjectBehavior;
use RetailExpress\SkyLink\Sdk\ValueObjects\SimpleStatus;
use ValueObjects\Exception\InvalidNativeArgumentException;
use ValueObjects\NullValue\NullValue;

class SimpleStatusSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedThrough('fromNative', [true]);
        $this->shouldHaveType(SimpleStatus::class);
    }

    public function it_asserts_a_boolean_argument()
    {
        $this->beConstructedThrough('fromNative', ['not a boolean']);
        $this->shouldThrow(InvalidNativeArgumentException::class)->duringInstantiation();
    }

    public function it_can_be_enabled()
    {
        $this->beConstructedThrough('fromNative', [true]);
        $this->isEnabled()->shouldBe(true);
        $this->isDisabled()->shouldBe(false);
    }

    public function it_can_be_disabled()
    {
        $this->beConstructedThrough('fromNative', [false]);
        $this->isDisabled()->shouldBe(true);
        $this->isEnabled()->shouldBe(false);
    }

    public function its_value_can_be_compared_against_an_equal()
    {
        $this->beConstructedThrough('fromNative', [true]);
        $this->sameValueAs(SimpleStatus::fromNative(true))->shouldBe(true);
    }

    public function its_value_can_be_compared_against_a_non_equal_of_the_same_type()
    {
        $this->beConstructedThrough('fromNative', [true]);
        $this->sameValueAs(SimpleStatus::fromNative(false))->shouldBe(false);
    }

    public function its_value_can_be_compared_against_a_non_equal_of_different_type()
    {
        $this->beConstructedThrough('fromNative', [true]);
        $this->sameValueAs(new NullValue())->shouldBe(false);
    }

    public function it_can_be_cast_to_a_native()
    {
        $this->beConstructedThrough('fromNative', [true]);
        $this->toNative()->shouldBe(true);
    }

    public function it_can_be_represented_as_a_string()
    {
        $this->beConstructedThrough('fromNative', [true]);
        $this->__toString()->shouldBe('1');
    }
}
