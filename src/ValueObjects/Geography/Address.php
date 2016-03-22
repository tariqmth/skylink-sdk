<?php

namespace RetailExpress\SkyLink\ValueObjects\Geography;

use BadMethodCallException;
use ReflectionClass;
use ValueObjects\Geography\Country;
use ValueObjects\Geography\CountryCodeName;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class Address implements ValueObjectInterface
{
    private static $countryAliases;

    private $line1;

    private $line2;

    private $line3;

    private $city;

    private $state;

    private $postcode;

    private $country;

    /**
     * Returns a new Address from native PHP arguments.
     *
     * @param string $line1
     * @param string $line2
     * @param string $line3
     * @param string $city
     * @param string $state
     * @param string $postcode
     * @param string $country
     *
     * @return self
     * @return ValueObjectInterface
     *
     * @throws BadMethodCallException
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) !== 7) {
            throw new BadMethodCallException('You must provide exactly 7 arguments: 1) line 1, 2) line 2, 3) line 3, 4) city, 5) state, 6) postcode, 7) country.');
        }

        $line1 = new StringLiteral($args[0]);
        $line2 = new StringLiteral($args[1]);
        $line3 = new StringLiteral($args[2]);
        $city = new StringLiteral($args[3]);
        $state = new StringLiteral($args[4]);
        $postcode = new StringLiteral($args[5]);

        $countryCode = isset($args[6]) ? self::getCountryCodeByName($args[6]) : null;
        $country = null;
        if (null !== $countryCode) {
            $country = Country::fromNative($countryCode);
        }

        return new self($line1, $line2, $line3, $city, $state, $postcode, $country);
    }

    private static function getCountryCodeByName($countryName)
    {
        $reflectedCountryCodes = new ReflectionClass(CountryCodeName::class);
        $names = $reflectedCountryCodes->getStaticProperties()['names'];

        // Firstly, short for a matching (case insenstive) country name from the list of proper country names
        $index = array_search(strtolower($countryName), array_map('strtolower', $names));
        if (false !== $index) {
            return $index;
        }

        // Failing that, load in the aliases
        foreach (self::getCountryAliases() as $countryCode => $aliases) {
            foreach ($aliases as $alias) {
                if (preg_match($alias, $countryName)) {
                    return $countryCode;
                }
            }
        }
    }

    private static function getCountryAliases()
    {
        if (null === self::$countryAliases) {
            self::$countryAliases = json_decode(file_get_contents(__DIR__.'/../../../resources/countries.json'));
        }

        return self::$countryAliases;
    }

    public function __construct(
        StringLiteral $line1,
        StringLiteral $line2,
        StringLiteral $line3,
        StringLiteral $city,
        StringLiteral $state,
        StringLiteral $postcode,
        Country $country = null
    ) {
        $this->line1 = $line1;
        $this->line2 = $line2;
        $this->line3 = $line3;
        $this->city = $city;
        $this->state = $state;
        $this->postcode = $postcode;
        $this->country = $country;
    }

    /**
     * Tells whether two Address instances are equal.
     *
     * @param ValueObjectInterface $address
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $address)
    {
        if (false === Util::classEquals($this, $address)) {
            return false;
        }

        return $this->getLine1()->sameValueAs($address->getLine1()) &&
            $this->getLine2()->sameValueAs($address->getLine2()) &&
            $this->getLine3()->sameValueAs($address->getLine3()) &&
            $this->getCity()->sameValueAs($address->getCity()) &&
            $this->getState()->sameValueAs($address->getState()) &&
            $this->getPostcode()->sameValueAs($address->getPostcode()) &&
            $this->getCountry()->sameValueAs($address->getCountry());
    }

    /**
     * Returns line 1.
     *
     * @return StringLiteral
     */
    public function getLine1()
    {
        return clone $this->line1;
    }

    /**
     * Returns line 2.
     *
     * @return StringLiteral
     */
    public function getLine2()
    {
        return clone $this->line2;
    }

    /**
     * Returns line 3.
     *
     * @return StringLiteral
     */
    public function getLine3()
    {
        return clone $this->line3;
    }

    /**
     * Returns the city.
     *
     * @return StringLiteral
     */
    public function getCity()
    {
        return clone $this->city;
    }

    /**
     * Returns the state.
     *
     * @return StringLiteral
     */
    public function getState()
    {
        return clone $this->state;
    }

    /**
     * Returns the postcode.
     *
     * @return StringLiteral
     */
    public function getPostcode()
    {
        return clone $this->postcode;
    }

    /**
     * Returns the country.
     *
     * @return Country
     */
    public function getCountry()
    {
        return isset($this->country) ? clone $this->country : null;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function __toString()
    {
        // Multi-dimensional array representing entire lines plus components of each line
        //
        // [
        //    {line 1},
        //    [
        //         [{part 1}, {part 2}]
        //    ]
        // ]
        $lines = [];

        if (!$this->getLine1()->isEmpty()) {
            $lines[] = $this->getLine1();
        }

        if (!$this->getLine2()->isEmpty()) {
            $lines[] = $this->getLine2();
        }

        if (!$this->getLine3()->isEmpty()) {
            $lines[] = $this->getLine3();
        }

        $localityLine = [];
        if (!$this->getCity()->isEmpty()) {
            $localityLine[] = $this->getCity();
        }
        if (!$this->getState()->isEmpty()) {
            $localityLine[] = $this->getState();
        }
        if (!$this->getPostcode()->isEmpty()) {
            $localityLine[] = $this->getPostcode();
        }
        if (count($localityLine) > 0) {
            $lines[] = $localityLine;
        }

        if (null !== $this->getCountry()) {
            $lines[] = $this->getCountry();
        }

        $lines = array_map(function ($line) {
            return is_array($line) ? implode(' ', $line) : $line;
        }, $lines);

        return implode("\n", $lines);
    }
}
