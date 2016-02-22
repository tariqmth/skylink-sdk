<?php

namespace RetailExpress\SkyLink\Customers;

/**
 * A huge thank you to Paul Ferrett for this useful ABN/ACN validator class.
 *
 * @link https://gist.github.com/paulferrett/8141303
 */

/**
 * ABN and ACN Validator Class.
 *
 * @author Paul Ferrett, 2009 (http://www.paulferrett.com)
 */
class AbnValidator
{
    /**
     * Return true if $number is a valid ABN.
     *
     * @param string $number
     *
     * @return bool True if $number is a valid ABN
     */
    public static function isValidAbnOrAcn($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        if (strlen($number) == 9) {
            return self::isValidAcn($number);
        }
        if (strlen($number) == 11) {
            return self::isValidAbn($number);
        }

        return false;
    }

    /**
     * Validate an Australian Business Number (ABN).
     *
     * @param string $abn
     *
     * @link http://www.ato.gov.au/businesses/content.asp?doc=/content/13187.htm
     *
     * @return bool True if $abn is a valid ABN, false otherwise
     */
    public static function isValidAbn($abn)
    {
        $weights = array(10, 1, 3, 5, 7, 9, 11, 13, 15, 17, 19);

        // Strip non-numbers from the acn
        $abn = preg_replace('/[^0-9]/', '', $abn);

        // Check abn is 11 chars long
        if (strlen($abn) != 11) {
            return false;
        }

        // Subtract one from first digit
        $abn[0] = ((int) $abn[0] - 1);

        // Sum the products
        $sum = 0;
        foreach (str_split($abn) as $key => $digit) {
            $sum += ($digit * $weights[$key]);
        }

        if (($sum % 89) != 0) {
            return false;
        }

        return true;
    }

    /**
     * Validate an Australian Company Number (ACN).
     *
     * @param string $acn
     *
     * @link http://www.asic.gov.au/asic/asic.nsf/byheadline/Australian+Company+Number+(ACN)+Check+Digit
     *
     * @return bool True if $acn is a valid ACN, false otherwise
     */
    public static function isValidAcn($acn)
    {
        $weights = array(8, 7, 6, 5, 4, 3, 2, 1, 0);

        // Strip non-numbers from the acn
        $acn = preg_replace('/[^0-9]/', '', $acn);

        // Check acn is 9 chars long
        if (strlen($acn) != 9) {
            return false;
        }

        // Sum the products
        $sum = 0;
        foreach (str_split($acn) as $key => $digit) {
            $sum += $digit * $weights[$key];
        }

        // Get the remainder
        $remainder = $sum % 10;

        // Get remainder compliment
        $complement = (string) (10 - $remainder);

        // If complement is 10, set to 0
        if ($complement === '10') {
            $complement = '0';
        }

        return $acn[8] === $complement;
    }
}

// Example Usage:
// AbnValidator::isValidAbnOrAcn("44 706 210 937")
//  => true

