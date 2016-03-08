<?php

namespace RetailExpress\SkyLink\Products;

use ValueObjects\Number\Real;

class PhysicalPackage
{
    const CUBIC_WEIGHT_CONVERSION_FACTOR = 250;

    private $weight;

    private $length;

    private $width;

    private $height;

    private $manualCubicWeight;

    public static function fromNative()
    {
        $args = func_get_args();

        return new self(
            new Real($args[0]),
            new Real($args[1]),
            new Real($args[2]),
            new Real($args[3]),
            isset($args[4]) ? new Real($args[4]) : null
        );
    }

    public function __construct(Real $weight, Real $length, Real $width, Real $height, Real $manualCubicWeight = null)
    {
        $this->weight = $weight;
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->manualCubicWeight = $manualCubicWeight;
    }

    public function getWeight()
    {
        return clone $this->weight;
    }

    public function getLength()
    {
        return clone $this->length;
    }

    public function getWidth()
    {
        return clone $this->width;
    }

    public function getHeight()
    {
        return clone $this->height;
    }

    /**
     * Returns the cubic weight of the physical package, either determined by the
     * manual cubic weight parameter, or dynamically calculated as per cubic
     * volumetric calculation guidelines provided by Australia Post (and
     * supported by the majority of couriers known to me).
     *
     * @link http://auspost.com.au/media/documents/APO0208_How_to_Cube_Guide_A4_V10.pdf
     * @return Real
     */
    public function getCubicWeight()
    {
        if (null !== $this->manualCubicWeight) {
            return clone $this->manualCubicWeight;
        }

        return new Real($this->getWeight()->toNative() *
            $this->getLength()->toNative() *
            $this->getHeight()->toNative() *
            self::CUBIC_WEIGHT_CONVERSION_FACTOR);
    }
}
