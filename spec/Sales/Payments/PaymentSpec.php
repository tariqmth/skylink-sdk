<?php

namespace spec\RetailExpress\SkyLink\Sales\Payments;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use LogicException;
use RetailExpress\SkyLink\Sales\Payments\Payment;
use RetailExpress\SkyLink\Sales\Payments\PaymentMethodId;

class PaymentSpec extends ObjectBehavior
{
    function it_is_initializable_as_a_normal_payment()
    {
        $this->beConstructedThrough('normalFromNative', [time(), 1, 10]);
        $this->shouldHaveType(Payment::class);
    }

    function it_is_initializable_using_a_voucher_with_code()
    {
        Payment::setVoucherMethodId(new PaymentMethodId(1));
        $this->beConstructedThrough('usingVoucherWithCodeFromNative', [time(), 'voucher code', 10]);
        Payment::forgetVoucherMethodId();
    }

    function it_forces_a_voucher_payment_method_to_be_defined()
    {
        $this->beConstructedThrough('usingVoucherWithCodeFromNative', [time(), 'voucher code', 10]);
        $this->shouldThrow(LogicException::class)->duringInstantiation();
    }

    function it_returns_method_id()
    {
        $this->beConstructedThrough('normalFromNative', [time(), 1, 10]);
        $this->getMethodId()->toNative()->shouldBe(1);
    }

    function it_returns_made_at()
    {
        $madeAt = time();
        $this->beConstructedThrough('normalFromNative', [$madeAt, 1, 10]);
        $this->getMadeAt()->getTimestamp()->shouldBe($madeAt);
    }

    function it_returns_total()
    {
        $this->beConstructedThrough('normalFromNative', [time(), 1, 10]);
        $this->getTotal()->toNative()->shouldBe(10.0);
    }

    function it_returns_voucher_code()
    {
        Payment::setVoucherMethodId(new PaymentMethodId(1));
        $this->beConstructedThrough('usingVoucherWithCodeFromNative', [time(), 'voucher code', 10]);
        $this->getVoucherCode()->toNative()->shouldBe('voucher code');
        $this->usesVoucherCode()->shouldBe(true);
        Payment::forgetVoucherMethodId();
    }

    function it_doesnt_return_any_voucher_code_if_no_voucher_was_used()
    {
        $this->beConstructedThrough('normalFromNative', [time(), 1, 10]);
        $this->getVoucherCode()->shouldBe(null);
        $this->usesVoucherCode()->shouldBe(false);
    }
}