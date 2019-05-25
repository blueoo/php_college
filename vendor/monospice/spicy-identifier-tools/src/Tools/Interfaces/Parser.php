<?php

namespace Monospice\SpicyIdentifiers\Tools\Interfaces;

/**
 * Parses an identifer string into an array of parts
 *
 * @author Cy Rossignol <cy.rossignol@yahoo.com>
 */
interface Parser
{

    /**
     * Parses an identifier string using the given format into an array of parts
     *
     * @api
     *
     * @param string $identifier The identifier string to parse
     * @param string $format The string constant representing the format of
     * the identifier
     *
     * @return array The array of identifier parts
     */
    public static function parse($identifier, $format);

    /**
     * Parses an identifier string containing multiple formats into an array of
     * parts
     *
     * @api
     *
     * @param string $identifier The identifier string to parse
     * @param array $formats The array of string constants representing the
     * formats of the identifier.
     *
     * @return array The array of identifier parts
     */
    public static function parseFromMixedCase($identifier, array $formats);

    /**
     * Parses an identifier string into an array of parts from camelCase
     *
     * @api
     *
     * @param string $identifier The identifier string to parse
     *
     * @return array The array of identifier parts
     */
    public static function parseFromCamelCase($identifier);

    /**
     * Parses an identifier string into an array of parts from camelCase
     * using the extended ASCII character set (0x7F-0xFF) according to ISO
     * 8859-1 (latin1) which is supported by PHP identifiers. Assumptions have
     * been made about what is considered an uppercase character (0xC0-0xDF) by
     * default for the purpose of splitting an identifier into words
     *
     * @api
     * @link http://www.iso.org/iso/catalogue_detail?csnumber=28245 The
     * ISO 8859-1:1998 character set standard
     *
     * @param string $ident The identifier string to parse
     * @param string $upper An regular expression string that contains a set of
     * escaped extended ASCII character representations that will be considered
     * uppercase
     * @param string $lower An regular expression string that contains a set of
     * escaped extended ASCII character representations that will be considered
     * lowercase
     *
     * @return array The array of identifier parts
     */
    public static function parseFromCamelCaseExtended($ident, $upper, $lower);

    /**
     * Parses an identifier string into an array of parts from underscore_case
     *
     * @api
     *
     * @param string $identifier The identifier string to parse
     *
     * @return array The array of identifier parts
     */
    public static function parseFromUnderscore($identifier);

    /**
     * Parses an identifier string into an array of parts from hyphenated_case
     *
     * @api
     *
     * @param string $identifier The identifier string to parse
     *
     * @return array The array of identifier parts
     */
    public static function parseFromHyphen($identifier);
}
