<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Payments;

use DateTimeImmutable;
use LogicException;
use RetailExpress\SkyLink\Sdk\Sales\Orders\OrderId;
use RetailExpress\SkyLink\Sdk\Vouchers\VoucherCode;
use Sabre\Xml\XmlSerializable;
use ValueObjects\Number\Real;

class Payment implements XmlSerializable
{
    use V2PaymentSerializer;

    private static $voucherMethodId;

    private $order;

    private $madeAt;

    private $methodId;

    private $total;

    private $voucherCode;

    private $id;

    public static function setVoucherMethodId(PaymentMethodId $methodId)
    {
        self::$voucherMethodId = $methodId;
    }

    public static function forgetVoucherMethodId()
    {
        self::$voucherMethodId = null;
    }

    public static function normalFromNative($orderId, $madeAt, $methodId, $total)
    {
        return self::normal(
            new OrderId($orderId),
            new DateTimeImmutable("@{$madeAt}"),
            new PaymentMethodId($methodId),
            new Real($total)
        );
    }

    public static function normal(OrderId $orderId, DateTimeImmutable $madeAt, PaymentMethodId $methodId, Real $total)
    {
        $payment = new self($orderId, $madeAt, $total);
        $payment = $payment->withMethodId($methodId);

        return $payment;
    }

    public static function usingVoucherWithCodeFromNative($orderId, $madeAt, $voucherCode, $total)
    {
        return self::usingVoucherWithCode(
            new OrderId($orderId),
            new DateTimeImmutable("@{$madeAt}"),
            new VoucherCode($voucherCode),
            new Real($total)
        );
    }

    public static function usingVoucherWithCode(OrderId $orderId, DateTimeImmutable $madeAt, VoucherCode $voucherCode, Real $total)
    {
        $payment = new self($orderId, $madeAt, $total);
        $payment = $payment->withVoucherCode($voucherCode);

        return $payment;
    }

    private function __construct(OrderId $orderId, DateTimeImmutable $madeAt, Real $total)
    {
        $this->orderId = $orderId;
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

    public function setId(PaymentId $id)
    {
        if (null !== $this->getId()) {
            throw new LogicException('Payment ID already set, cannot override.');
        }

        $this->id = $id;
    }

    public function getOrderId()
    {
        return clone $this->orderId;
    }

    public function getMadeAt()
    {
        return clone $this->madeAt;
    }

    public function getMethodId()
    {
        return clone $this->methodId;
    }

    public function getTotal()
    {
        return clone $this->total;
    }

    public function getVoucherCode()
    {
        if (null === $this->voucherCode) {
            return null;
        }

        return clone $this->voucherCode;
    }

    public function usesVoucherCode()
    {
        return (bool) $this->getVoucherCode();
    }

    public function getId()
    {
        if (null === $this->id) {
            return null;
        }

        return clone $this->id;
    }
}
