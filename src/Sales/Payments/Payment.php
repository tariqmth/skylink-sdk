<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Payments;

use DateTimeImmutable;
use LogicException;
use RetailExpress\SkyLink\Sdk\Vouchers\VoucherCode;
use ValueObjects\Number\Real;

class Payment
{
    private static $voucherMethodId;

    private $methodId;

    private $madeAt;

    private $total;

    private $voucherCode;

    public static function setVoucherMethodId(PaymentMethodId $methodId)
    {
        self::$voucherMethodId = $methodId;
    }

    public static function forgetVoucherMethodId()
    {
        self::$voucherMethodId = null;
    }

    public static function normalFromNative($madeAt, $methodId, $total)
    {
        return self::normal(
            new DateTimeImmutable("@{$madeAt}"),
            new PaymentMethodId($methodId),
            new Real($total)
        );
    }

    public static function normal(DateTimeImmutable $madeAt, PaymentMethodId $methodId, Real $total)
    {
        $payment = new self($madeAt, $total);
        $payment = $payment->withMethodId($methodId);

        return $payment;
    }

    public static function usingVoucherWithCodeFromNative($madeAt, $voucherCode, $total)
    {
        return self::usingVoucherWithCode(
            new DateTimeImmutable("@{$madeAt}"),
            new VoucherCode($voucherCode),
            new Real($total)
        );
    }

    public static function usingVoucherWithCode(DateTimeImmutable $madeAt, VoucherCode $voucherCode, Real $total)
    {
        $payment = new self($madeAt, $total);
        $payment = $payment->withVoucherCode($voucherCode);

        return $payment;
    }

    private function __construct(DateTimeImmutable $madeAt, Real $total)
    {
        $this->madeAt = $madeAt;
        $this->total = $total;
    }

    private function withMethodId(PaymentMethodId $methodId)
    {
        $new = clone $this;
        $new->methodId = $methodId;

        return $new;
    }

    private function withVoucherCode(VoucherCode $voucherCode)
    {
        if (null === self::$voucherMethodId) {
            $message = 'A voucher payment method id must be setup in order to associate a voucher code.';
            throw new LogicException($message);
        }

        $new = clone $this;
        $new->voucherCode = $voucherCode;

        return $new;
    }

    public function getMethodId()
    {
        return clone $this->methodId;
    }

    public function getMadeAt()
    {
        return clone $this->madeAt;
    }

    public function getTotal()
    {
        return clone $this->total;
    }

    public function getVoucherCode()
    {
        if (null === $this->voucherCode) {
            return;
        }

        return clone $this->voucherCode;
    }

    public function usesVoucherCode()
    {
        return (bool) $this->getVoucherCode();
    }
}
