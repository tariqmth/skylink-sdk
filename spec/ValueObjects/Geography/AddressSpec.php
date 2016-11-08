<?php

namespace spec\RetailExpress\SkyLink\Sdk\ValueObjects\Geography;

use PhpSpec\ObjectBehavior;
use BadMethodCallException;
use RetailExpress\SkyLink\Sdk\ValueObjects\Geography\Address;
use ValueObjects\NullValue\NullValue;

class AddressSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedThrough('fromNative', $this->get_address_arguments());
        $this->shouldHaveType(Address::class);
    }

    public function it_checks_the_number_of_arguments()
    {
        $this->beConstructedThrough('fromNative', []);
        $this->shouldThrow(BadMethodCallException::class)->duringInstantiation();
    }

    public function it_returns_line_1()
    {
        $arguments = $this->get_address_arguments();
        $this->beConstructedThrough('fromNative', $arguments);
        $this->getLine1()->toNative()->shouldBe($arguments[0]);
    }

    public function it_returns_line_2()
    {
        $arguments = $this->get_address_arguments();
        $this->beConstructedThrough('fromNative', $arguments);
        $this->getLine2()->toNative()->shouldBe($arguments[1]);
    }

    public function it_returns_line_3()
    {
        $arguments = $this->get_address_arguments();
        $this->beConstructedThrough('fromNative', $arguments);
        $this->getLine3()->toNative()->shouldBe($arguments[2]);
    }

    public function it_returns_city()
    {
        $arguments = $this->get_address_arguments();
        $this->beConstructedThrough('fromNative', $arguments);
        $this->getCity()->toNative()->shouldBe($arguments[3]);
    }

    public function it_returns_state()
    {
        $arguments = $this->get_address_arguments();
        $this->beConstructedThrough('fromNative', $arguments);
        $this->getState()->toNative()->shouldBe($arguments[4]);
    }

    public function it_returns_postcode()
    {
        $arguments = $this->get_address_arguments();
        $this->beConstructedThrough('fromNative', $arguments);
        $this->getPostcode()->toNative()->shouldBe($arguments[5]);
    }

    public function it_returns_country()
    {
        $arguments = $this->get_address_arguments();
        $this->beConstructedThrough('fromNative', $arguments);
        $this->getCountry()->getName()->toNative()->shouldBe($arguments[6]);
    }

    public function it_uses_country_aliases_to_return_the_country()
    {
        $arguments = $this->get_address_arguments();
        $arguments[6] = 'aus.';
        $this->beConstructedThrough('fromNative', $arguments);
        $this->getCountry()->getName()->toNative()->shouldBe('Australia');
    }

    public function it_ignores_invalid_country_aliases()
    {
        $arguments = $this->get_address_arguments();
        $arguments[6] = 'invalid country name';
        $this->beConstructedThrough('fromNative', $arguments);
        $this->getCountry()->shouldBe(null);
    }

    public function its_value_can_be_compared_against_an_equal()
    {
        $address = forward_static_call_array(
            [Address::class, 'fromNative'],
            $this->get_address_arguments()
        );

        $this->beConstructedThrough('fromNative', $this->get_address_arguments());
        $this->sameValueAs($address)->shouldBe(true);
    }

    public function its_value_can_be_compared_against_a_non_equal_of_the_same_type()
    {
        $arguments = $this->get_address_arguments();
        $arguments[0] = 'different street 1';

        $address = forward_static_call_array(
            [Address::class, 'fromNative'],
            $arguments
        );

        $this->beConstructedThrough('fromNative', $this->get_address_arguments());
        $this->sameValueAs($address)->shouldBe(false);
    }

    public function its_value_can_be_compared_against_a_non_equal_of_different_type()
    {
        $this->beConstructedThrough('fromNative', $this->get_address_arguments());
        $this->sameValueAs(new NullValue())->shouldBe(false);
    }

    public function it_can_be_represented_as_a_string()
    {
        $this->beConstructedThrough('fromNative', $this->get_address_arguments());

        $expected = <<<'ADDRESS'
Unit 11
Pitt Street Mall
Pitt Street
Sydney New South Wales 2000
Australia
ADDRESS;

        $this->__toString()->shouldBe($expected);
    }

    public function get_address_arguments()
    {
        return [
            'Unit 11',
            'Pitt Street Mall',
            'Pitt Street',
            'Sydney',
            'New South Wales',
            '2000',
            'Australia',
        ];
    }
}
