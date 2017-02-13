<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Eta;

use BadMethodCallException;
use DateTimeImmutable;
use InvalidArgumentException;
use RetailExpress\SkyLink\Sdk\Catalogue\Products\ProductId;
use RetailExpress\SkyLink\Sdk\ValueObjects\SalesChannelId;
use Sabre\Xml\XmlDeserializable;
use ValueObjects\Number\Real;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class Eta implements ValueObjectInterface, XmlDeserializable
{
    use V2EtaDeserializer;

    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var EtaQty
     */
    private $qty;

    /**
     * @var DateTimeImmutable
     */
    private $date;

    /**
     * Returns an ETA object taking PHP native values as arguments.
     *
     * @return AttributeOption
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 3) {
            throw new BadMethodCallException('You must provide at least 3 argument: 1) product id, 2) qty (greater than 0), 3) date');
        }

        $productId = new ProductId($args[0]);
        $qty = EtaQty::fromNative($args[1]);
        $date = new DateTimeImmutable("@{$args[2]}");

        return new self($productId, $qty, $date);
    }

    public function __construct(ProductId $productId, EtaQty $qty, DateTimeImmutable $date)
    {
        $this->productId = $productId;
        $this->qty = $qty;
        $this->date = $date;
    }

    public function getProductId()
    {
        return clone $this->productId;
    }

    public function getQty()
    {
        return clone $this->qty;
    }

    public function getDate()
    {
        return clone $this->date;
    }

    /**
     * Tells whether two ETAE Qty instances are equal.
     *
     * @param ValueObjectInterface $eta
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $eta)
    {
        if (false === Util::classEquals($this, $eta)) {
            return false;
        }

        return $this->getProductId()->sameValueAs($eta->getProductId()) &&
            $this->getQty()->sameValueAs($eta->getQty()) &&
            $this->getDate()->sameValueAs($eta->getDate());
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->date;
    }
}
