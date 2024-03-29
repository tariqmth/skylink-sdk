<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Payments;

use RetailExpress\SkyLink\Sdk\ValueObjects\SimpleStatus;
use Sabre\Xml\XmlDeserializable;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class PaymentMethod implements ValueObjectInterface, XmlDeserializable
{
    use V2PaymentMethodDeserializer;

    private $id;

    private $name;

    private $status;

    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 3) {
            throw new BadMethodCallException('You must provide at least 3 arguments: 1) id, 2) name, 3) status');
        }

        $id = new PaymentMethodId($args[0]);
        $name = new StringLiteral($args[1]);
        $status = SimpleStatus::fromNative($args[2]);

        return new self($id, $name, $status);
    }

    public function __construct(PaymentMethodId $id, StringLiteral $name, SimpleStatus $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->status = $status;
    }

    public function getId()
    {
        return clone $this->id;
    }

    public function getName()
    {
        return clone $this->name;
    }

    public function getStatus()
    {
        return clone $this->status;
    }

    public function sameValueAs(ValueObjectInterface $paymentMethod)
    {
        if (false === Util::classEquals($this, $paymentMethod)) {
            return false;
        }

        return $this->getId()->sameValueAs($paymentMethod->getId()) &&
            $this->getName()->sameValueAs($paymentMethod->getName()) &&
            $this->getStatus()->sameValueAs($paymentMethod->getStatus());
    }

    public function __toString()
    {
        return (string) $this->getId();
    }
}
