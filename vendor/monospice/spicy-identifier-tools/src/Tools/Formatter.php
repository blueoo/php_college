<?php

namespace Monospice\SpicyIdentifiers\Tools;

use Monospice\SpicyIdentifiers\Tools\Interfaces;

/**
 * Formats an array of identifier parts into a string
 *
 * @author Cy Rossignol <cy.rossignol@yahoo.com>
 */
class Formatter implements Interfaces\Formatter
{

    // Inherit Doc from Interfaces\Formatter
    public static function format(array $parts, $format)
    {
        $formatFunction = 'format' . $format;

        return static::$formatFunction($parts);
    }


    // Inherit Doc from Interfaces\Formatter
    public static function formatUppercase(array $parts)
    {
        return strtoupper(implode('', $parts));
    }


    // Inherit Doc from Interfaces\Formatter
    public static function formatLowercase(array $parts)
    {
        return strtolower(implode('', $parts));
    }


    // Inherit Doc from Interfaces\Formatter
    public static function formatCamelCase(array $parts)
    {
        return lcfirst(
            implode('', array_map(function($p) {
                return static::ucfirstAndLower($p);
            }, $parts))
        );
    }


    // Inherit Doc from Interfaces\Formatter
    public static function formatUpperCamelCase(array $parts)
    {
        return implode('', array_map(function($p) {
            return static::ucfirstAndLower($p);
        }, $parts));
    }


    // Inherit Doc from Interfaces\Formatter
    public static function formatCamelCaseWithAcronyms(array $parts)
    {
        $camelCase = array_map(function($p) {
            return static::ucfirstAndLowerNonAcronym($p);
        }, $parts);

        if (static::isAcronym($camelCase[0])) {
            return implode('', $camelCase);
        }

        return lcfirst(implode('', $camelCase));
    }


    // Inherit Doc from Interfaces\Formatter
    public static function formatUpperCamelCaseWithAcronyms(array $parts)
    {
        return implode('', array_map(function($p) {
            return static::ucfirstAndLowerNonAcronym($p);
        }, $parts));
    }


    // Inherit Doc from Interfaces\Formatter
    public static function formatUnderscore(array $parts)
    {
        return implode('_', array_map('strtolower', $parts));
    }


    // Inherit Doc from Interfaces\Formatter
    public static function formatUpperUnderscore(array $parts)
    {
        return implode('_', array_map(function($p) {
            return static::ucfirstAndLower($p);
        }, $parts));
    }


    // Inherit Doc from Interfaces\Formatter
    public static function formatCapsUnderscore(array $parts)
    {
        return implode('_', array_map('strtoupper', $parts));
    }


    // Inherit Doc from Interfaces\Formatter
    public static function formatUnderscoreWithAcronyms(array $parts)
    {
        return implode('_', array_map(function($p) {
            return static::lowerNonAcronym($p);
        }, $parts));
    }


    // Inherit Doc from Interfaces\Formatter
    public static function formatUpperUnderscoreWithAcronyms(array $parts)
    {
        return implode('_', array_map(function($p) {
            return static::ucfirstAndLowerNonAcronym($p);
        }, $parts));
    }


    // Inherit Doc from Interfaces\Formatter
    public static function formatHyphen(array $parts)
    {
        return implode('-', array_map('strtolower', $parts));
    }


    // Inherit Doc from Interfaces\Formatter
    public static function formatUpperHyphen(array $parts)
    {
        return implode('-', array_map(function($p) {
            return static::ucfirstAndLower($p);
        }, $parts));
    }


    // Inherit Doc from Interfaces\Formatter
    public static function formatCapsHyphen(array $parts)
    {
        return implode('-', array_map('strtoupper', $parts));
    }


    // Inherit Doc from Interfaces\Formatter
    public static function formatHyphenWithAcronyms(array $parts)
    {
        return implode('-', array_map(function($p) {
            return static::lowerNonAcronym($p);
        }, $parts));
    }


    // Inherit Doc from Interfaces\Formatter
    public static function formatUpperHyphenWithAcronyms(array $parts)
    {
        return implode('-', array_map(function($p) {
            return static::ucfirstAndLowerNonAcronym($p);
        }, $parts));
    }


    /**
     * Determine if a string is an acronym (all caps)
     *
     * @param string $string The input string
     *
     * @return bool True if the string is all caps
     */
    protected static function isAcronym($string)
    {
        return strtoupper($string) === $string;
    }


    /**
     * Convert a string into a format with the first character capitalized and
     * the remaining characters lowercased
     *
     * @param string $string The input string
     *
     * @return string The converted string
     */
    protected static function ucfirstAndLower($string)
    {
        return ucfirst(strtolower($string));
    }


    /**
     * Convert a non-acronym (not all caps) string to lowercase
     *
     * @param string $string The input string
     *
     * @return string The converted string
     */
    protected static function lowerNonAcronym($string)
    {
        if (static::isAcronym($string)) {
            return $string;
        }

        return strtolower($string);
    }


    /**
     * Convert a non-acronym (not all caps) string to a lowercase string with
     * the first character uppercased
     *
     * @param string $string The input string
     *
     * @return string The converted string
     */
    protected static function ucfirstAndLowerNonAcronym($string)
    {
        if (static::isAcronym($string)) {
            return $string;
        }

        return static::ucfirstAndLower($string);
    }
}
