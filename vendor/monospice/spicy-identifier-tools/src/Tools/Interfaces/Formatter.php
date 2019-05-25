<?php

namespace Monospice\SpicyIdentifiers\Tools\Interfaces;

/**
 * Formats an array of identifier parts into a string
 *
 * @author Cy Rossignol <cy.rossignol@yahoo.com>
 */
interface Formatter
{

    /**
     * Formats an array of identifier parts into an identifier string
     *
     * @param array $parts The array of identifier parts
     * @param string $format The string constant represnting the output format
     *
     * @return string The formatted identifier string
     */
    public static function format(array $parts, $format);

    /**
     * Formats the identifer to ALLUPPERCASE (with no delimiter)
     *
     * @param array $parts The array of identifier parts
     *
     * @return string The formatted identifier string
     */
    public static function formatUppercase(array $parts);

    /**
     * Formats the identifer to alllowercase (with no delimiter)
     *
     * @param array $parts The array of identifier parts
     *
     * @return string The formatted identifier string
     */
    public static function formatLowercase(array $parts);

    /**
     * Formats the identifier to camelCase
     *
     * @param array $parts The array of identifier parts
     *
     * @return string The formatted identifier string
     */
    public static function formatCamelCase(array $parts);

    /**
     * Formats the identifier to UpperCamelCase
     *
     * @param array $parts The array of identifier parts
     *
     * @return string The formatted identifier string
     */
    public static function formatUpperCamelCase(array $parts);

    /**
     * Formats the identifier to camelCaseWithACRNMS (with Acronyms)
     *
     * @param array $parts The array of identifier parts
     *
     * @return string The formatted identifier string
     */
    public static function formatCamelCaseWithAcronyms(array $parts);

    /**
     * Formats the identifier to UpperCamelCaseWithACRNMS (with Acronyms)
     *
     * @param array $parts The array of identifier parts
     *
     * @return string The formatted identifier string
     */
    public static function formatUpperCamelCaseWithAcronyms(array $parts);

    /**
     * Formats the identifier to underscore_case
     *
     * @param array $parts The array of identifier parts
     *
     * @return string The formatted identifier string
     */
    public static function formatUnderscore(array $parts);

    /** * Formats the identifier to Upper_Underscore_Case
     *
     * @param array $parts The array of identifier parts
     *
     * @return string The formatted identifier string
     */
    public static function formatUpperUnderscore(array $parts);

    /**
     * Formats the identifier to CAPITALIZED_UNDERSCORE_CASE
     *
     * @param array $parts The array of identifier parts
     *
     * @return string The formatted identifier string
     */
    public static function formatCapsUnderscore(array $parts);

    /**
     * Formats the identifier to underscore_case_with_ACRNMS (with Acronyms)
     *
     * @param array $parts The array of identifier parts
     *
     * @return string The formatted identifier string
     */
    public static function formatUnderscoreWithAcronyms(array $parts);

    /**
     * Formats the identifier to Upper_Underscore_Case_With_ACRNMS (Acronyms)
     *
     * @param array $parts The array of identifier parts
     *
     * @return string The formatted identifier string
     */
    public static function formatUpperUnderscoreWithAcronyms(array $parts);

    /**
     * Formats the identifier to hyphenated-case
     *
     * @param array $parts The array of identifier parts
     *
     * @return string The formatted identifier string
     */
    public static function formatHyphen(array $parts);

    /**
     * Formats the identifier to Upper-Hyphenated-Case
     *
     * @param array $parts The array of identifier parts
     *
     * @return string The formatted identifier string
     */
    public static function formatUpperHyphen(array $parts);

    /**
     * Formats the identifier to CAPITALIZED-HYPHENATED-CASE
     *
     * @param array $parts The array of identifier parts
     *
     * @return string The formatted identifier string
     */
    public static function formatCapsHyphen(array $parts);

    /**
     * Formats the identifier to hyphenated-case-with-ACRNMS (with Acronyms)
     *
     * @param array $parts The array of identifier parts
     *
     * @return string The formatted identifier string
     */
    public static function formatHyphenWithAcronyms(array $parts);

    /**
     * Formats the identifier to Upper-Hyphenated-Case-With-ACRNMS (Acronyms)
     *
     * @param array $parts The array of identifier parts
     *
     * @return string The formatted identifier string
     */
    public static function formatUpperHyphenWithAcronyms(array $parts);
}
