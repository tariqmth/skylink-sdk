<?php

namespace RetailExpress\SkyLink;

class XmlKeySanitiser
{
    /**
     * Array of filters to apply prior to sanitising to snake_case. This can
     * aid with edge-cases such as "SKU" or "POS" occuring.
     *
     * @var array
     */
    private static $filters = [
        'SKU' => 'Sku',
        'POS' => 'Pos',
    ];

    /**
     * Takes XML keys in the format "{}PascalCasedKey", strips brackets and
     * converts to the snake_case equivilent.
     *
     * @param string|array $payload
     *
     * @return string|array
     */
    public static function sanitise($payload)
    {
        // Handle key/value associated arrays (no nesting)
        if (is_array($payload)) {
            $sanitisedPayload = [];

            foreach ($payload as $key => $value) {
                $sanitisedPayload[self::sanitise($key)] = $value;
            }

            return $sanitisedPayload;
        }

        // Strip the brackets from the start of the payload
        $payload = preg_replace('/^{}/', '', $payload);

        // Apply filters against payload
        $payload = str_replace(array_keys(self::$filters), array_values(self::$filters), $payload);

        return snake_case($payload);
    }
}
