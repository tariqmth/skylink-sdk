<?php

namespace RetailExpress\SkyLink\Products;

use ValueObjects\StringLiteral\StringLiteral;

class Attribute
{
    private $code;

    private $options = [];

    /**
     * Creates a new Attribute, taking native PHP arguments. The first argument
     * is the attribute code, the second argument being an array available
     * options. Each option may be provided as a singular string or as a
     * key/value array at which point the key is chosen as the
     * identifier for the attribute. If no key is provided,
     * the value is used as the key.
     *
     * @param string $code
     * @param array  $options
     *
     * @return Attribute
     */
    public static function fromNative()
    {
        $args = func_get_args();

        $attribute = new self(AttributeCode::fromNative($args[0]));

        foreach ($args[1] as $option) {
            if (is_array($option)) {
                $label = (string) current(array_keys($option));
                $id = (string) current(array_values($option));

                $option = new AttributeOption(
                    $attribute->withoutOptions(),
                    new StringLiteral($label),
                    new StringLiteral($id)
                );
            } else {
                $option = new AttributeOption(
                    $attribute->withoutOptions(),
                    new StringLiteral($option),
                    new StringLiteral($option)
                );
            }

            $attribute = $attribute->withOption($option);
        }

        return $attribute;
    }

    public function __construct(AttributeCode $code)
    {
        $this->code = $code;
    }

    public function withOption(AttributeOption $option)
    {
        $new = clone $this;
        $new->options[] = $option;

        return $new;
    }

    public function withoutOptions()
    {
        $new = clone $this;
        $new->options = [];

        return $new;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getOptions()
    {
        return clone $this->options;
    }
}
