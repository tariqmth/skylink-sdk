<?php

namespace spec\RetailExpress\SkyLink\Sdk\Sales\Payments;

use PhpSpec\ObjectBehavior;
use LogicException;
use RetailExpress\SkyLink\Sdk\Sales\Payments\Payment;
use RetailExpress\SkyLink\Sdk\Sales\Payments\PaymentMethodId;

class PaymentSpec extends ObjectBehavior
{
    public function it_is_initializable_as_a_normal_payment()
    {
        $this->beConstructedThrough('normalFromNative', ['1', time(), 1, 10]);
        $this->shouldHaveType(Payment::class);
    }

    public function it_is_initializable_using_a_voucher_with_code()
    {
        Payment::setVoucherMethodId(new PaymentMethodId(1));
        $this->beConstructedThrough('usingVoucherWithCodeFromNative', ['1', time(), 'voucher code', 10]);
        Payment::forgetVoucherMethodId();
    }

    public function it_forces_a_voucher_payment_method_to_be_defined()
    {
        $this->beConstructedThrough('usingVoucherWithCodeFromNative', ['1', time(), 'voucher code', 10]);
        $this->shouldThrow(LogicException::class)->duringInstantiation();
    }

    public function it_returns_an_order_id()
    {
        $orderId = '1';
        $this->beConstructedThrough('normalFromNative', [$orderId, time(), 1, 10]);
        $this->getOrderId()->toNative()->shouldBe($orderId);
    }

    public function it_returns_method_id()
    {
        $methodId = 1;
        $this->beConstructedThrough('normalFromNative', ['1', time(), $methodId, 10]);
        $this->getMethodId()->toNative()->shouldBe($methodId);
    }

    public function it_returns_made_at()
    {
        $madeAt = time();
        $this->beConstructedThrough('normalFromNative', ['1', $madeAt, 1, 10]);
        $this->getMadeAt()->getTimestamp()->shouldBe($madeAt);
    }

    public function it_returns_total()
    {
        $this->beConstructedThrough('normalFromNative', ['1', time(), 1, 10]);
        $this->getTotal()->toNative()->shouldBe(10.0);
    }

    public function it_returns_voucher_code()
    {
        Payment::setVoucherMethodId(new PaymentMethodId(1));
        $this->beConstructedThrough('usingVoucherWithCodeFromNative', ['1', time(), 'voucher code', 10]);
        $this->getVoucherCode()->toNative()->shouldBe('voucher code');
        $this->usesVoucherCode()->shouldBe(true);
        Payment::forgetVoucherMethodId();
    }

    public function it_doesnt_return_any_voucher_code_if_no_voucher_was_used()
    {
        $this->beConstructedThrough('normalFromNative', ['1', time(), 1, 10]);
        $this->getVoucherCode()->shouldBe(null);
        $this->usesVoucherCode()->shouldBe(false);
    }
}
