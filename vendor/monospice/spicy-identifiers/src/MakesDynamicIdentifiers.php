<?php

namespace Monospice\SpicyIdentifiers;

use Monospice\SpicyIdentifiers\Tools\Parser;

/**
 * Contains factory methods that help to instantiate new instances of
 * DynamicIdentifier classes
 *
 * @category Package
 * @package  Monospice\SpicyIdentifiers
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     https://github.com/monospice/spicy-identifiers
 */
trait MakesDynamicIdentifiers
{
    /**
     * Make an instance and use the specified identifier as a single part
     * without parsing it
     *
     * Sometimes we may wish to use the dynamic features of the classes in this
     * package, but we don't need to parse the identifier string into its
     * component parts. In these cases, we can use this method to simply create
     * an instance for the identifier string without parsing it to improve
     * performance.
     *
     * @param string $identifier The identifier name string to use
     *
     * @return self An instance of this class with one identifier part that
     * equals the provided identifier string
     */
    public static function from($identifier)
    {
        return new static([ $identifier ]);
    }

    /**
     * Make an instance with the provided identifier parts without parsing
     * them
     *
     * Sometimes we may wish to use the dynamic features of the classes in this
     * package, but we already have a set of identifier parts that we'd like to
     * use. In these cases, we can use this method to simply create an instance
     * for the provided parts without parsing them to improve performance.
     *
     * @param array $identifierParts The series of strings that represent the
     * identifier when combined
     *
     * @return self An instance of this class for the provided identifier parts
     */
    public static function fromParts(array $identifierParts)
    {
        return new static($identifierParts);
    }

    /**
     * DEPRECATED: Use self::from() instead
     *
     * @param string $identifier The identifier name string to load
     *
     * @return self An instance of this class with one identifier part that
     * equals the provided identifier string
     *
     * @deprecated Use self::from() instead
     */
    public static function load($identifier)
    {
        return static::from($identifier);
    }

    /**
     * Make an instance by breaking an identifier name into parts using the
     * default case format specified on the exhibiting class
     *
     * @param string $identifier The identifier name string to parse
     *
     * @return self An instance of this class with the parsed identifier parts
     */
    public static function parse($identifier)
    {
        return new static(Parser::parse($identifier, static::$defaultCase));
    }

    /**
     * Make an instance by breaking an identifier into parts based on words
     * starting with uppercase characters in camel case
     *
     * @param string $identifier The identifier name string to parse
     *
     * @return self An instance of this class with the parsed identifier parts
     */
    public static function parseFromCamelCase($identifier)
    {
        return new static(Parser::parseFromCamelCase($identifier));
    }

    /**
     * Make an instance by breaking an identifier containing extended ASCII
     * characters into parts based on words starting with uppercase characters
     * in camel case
     *
     * @param string $identifier The identifier name string to parse
     *
     * @return self An instance of this class with the parsed identifier parts
     */
    public static function parseFromCamelCaseExtended($identifier)
    {
        return new static(Parser::parseFromCamelCaseExtended($identifier));
    }

    /**
     * Make an instance by breaking an identifier into parts based on words
     * seperated by underscores
     *
     * @param string $identifier The identifier name string to parse
     *
     * @return self An instance of this class with the parsed identifier parts
     */
    public static function parseFromUnderscore($identifier)
    {
        return new static(Parser::parseFromUnderscore($identifier));
    }

    /**
     * An alias for self::parseFromUnderscore()
     *
     * @param string $identifier The identifier name string to parse
     *
     * @return self An instance of this class with the parsed identifier parts
     */
    public static function parseFromSnakeCase($identifier)
    {
        return static::parseFromUnderscore($identifier);
    }

    /**
     * Make an instance by breaking an identifier into parts based on words
     * seperated by hyphens
     *
     * @param string $identifier The identifier name string to parse
     *
     * @return self An instance of this class with the parsed identifier parts
     */
    public static function parseFromHyphen($identifier)
    {
        return new static(Parser::parseFromHyphen($identifier));
    }

    /**
     * Make an instance by breaking an identifier into parts based on words
     * seperated by multiple case formats
     *
     * @param string $identifier The identifier name string to parse
     * @param array  $formats    The string constants representing the case
     * formats to attempt to parse the identifier from
     *
     * @return self An instance of this class with the parsed identifier parts
     *
     * @see \Monospice\SpicyIdentifiers\Tools\CaseFormat For the available
     * case formats
     */
    public static function parseFromMixedCase($identifier, array $formats)
    {
        return new static(Parser::parseFromMixedCase($identifier, $formats));
    }
}
