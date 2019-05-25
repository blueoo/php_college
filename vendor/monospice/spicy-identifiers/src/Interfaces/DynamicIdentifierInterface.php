<?php

namespace Monospice\SpicyIdentifiers\Interfaces;

use ArrayAccess;
use Countable;

/**
 * Parses and manipulates identifier names. Useful when working with dynamic
 * method, function, class, and variable names
 *
 * @category Package
 * @package  Monospice\SpicyIdentifiers
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     https://github.com/monospice/spicy-identifiers
 */
interface DynamicIdentifierInterface extends ArrayAccess, Countable
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
    public static function from($identifier);

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
    public static function fromParts(array $identifierParts);

    /**
     * Make an instance by breaking an identifier name into parts using the
     * default case format specified on the class
     *
     * @param string $identifier The identifier name string to parse
     *
     * @return self An instance of this class with the parsed identifier parts
     */
    public static function parse($identifier);

    /**
     * Make an instance by breaking an identifier into parts based on words
     * starting with uppercase characters in camel case
     *
     * @param string $identifier The identifier name string to parse
     *
     * @return self An instance of this class with the parsed identifier parts
     */
    public static function parseFromCamelCase($identifier);

    /**
     * Make an instance by breaking an identifier containing extended ASCII
     * characters into parts based on words starting with uppercase characters
     * in camel case
     *
     * @param string $identifier The identifier name string to parse
     *
     * @return self An instance of this class with the parsed identifier parts
     */
    public static function parseFromCamelCaseExtended($identifier);

    /**
     * Make an instance by breaking an identifier into parts based on words
     * seperated by underscores
     *
     * @param string $identifier The identifier name string to parse
     *
     * @return self An instance of this class with the parsed identifier parts
     */
    public static function parseFromUnderscore($identifier);

    /**
     * An alias for self::parseFromUnderscore()
     *
     * @param string $identifier The identifier name string to parse
     *
     * @return self An instance of this class with the parsed identifier parts
     */
    public static function parseFromSnakeCase($identifier);

    /**
     * Make an instance by breaking an identifier into parts based on words
     * seperated by hyphens
     *
     * @param string $identifier The identifier name string to parse
     *
     * @return self An instance of this class with the parsed identifier parts
     */
    public static function parseFromHyphen($identifier);

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
    public static function parseFromMixedCase($identifier, array $formats);

    /**
     * Explicitly set the case format to use for outputting the identifier
     * or for actions that use the string representation of the identifier
     *
     * If not explicitly set, the output case will default to the classes's
     * default case format
     *
     * @param string $caseFormat The string constant representing an output
     * case format
     *
     * @return $this The current instance of this class for method chaining
     *
     * @throws \InvalidArgumentException If not provided with a valid output
     * case format
     *
     * @see \Monospice\SpicyIdentifiers\Tools\CaseFormat For the available
     * output case formats
     */
    public function setOutputFormat($caseFormat);

    /**
     * Get the case format to use for outputting the identifier or for actions
     * that use the string representation of the identifier
     *
     * @return string The constant representing an output case format
     */
    public function getOutputFormat();

    /**
     * Get the current parts that make the whole identifier when combined
     *
     * @return array The strings that form the identifier name
     */
    public function parts();

    /**
     * Get the value of the identifier part at the specified offset
     *
     * @param int $offset The index of the identifier part to retrieve, starting
     * from zero
     *
     * @return string|null The name of the corresponding identifier part, or
     * NULL if a part doesn't exist at the specified offset
     */
    public function part($offset);

    /**
     * Get the keys of the identifier parts that match the specified string, or
     * the keys of all parts if no string provided
     *
     * @param string|null $search        The value of the identifier part to
     * match
     * @param bool        $caseSensitive Perform a case-sensitive comparison.
     * Defaults to FALSE
     *
     * @return array The array of matching integer keys
     */
    public function keys($search = null, $caseSensitive = false);

    /**
     * Get the number of identifier parts in the parsed identifier name
     *
     * @return int The number of identifier parts
     */
    public function getNumParts();

    /**
     * Get the identifier part at the beginning of the identifier name
     *
     * @return string The value of the first identifier part
     */
    public function first();

    /**
     * Get the identifier part at the end of the identifier name
     *
     * @return string The value of the last identifier part
     */
    public function last();

    /**
     * Get the string representation of the parsed identifier
     *
     * @return string The identifier parts combined into a single string in
     * the current case format
     */
    public function name();

    /**
     * Determine if the identifier contains a part at the specified offset
     *
     * @param int $offset The index of the identifier part to check
     *
     * @return bool True if the identifier parts array contains a value at the
     * specified offset
     */
    public function has($offset);

    /**
     * Determine if the identifier starts with the specified part string
     *
     * @param string $startsWith    The string that the first part should equal
     * @param bool   $caseSensitive Perform a case-sensitive comparison.
     * Defaults to FALSE
     *
     * @return bool True if the first identifier part equals the specified
     * string
     */
    public function startsWith($startsWith, $caseSensitive = false);

    /**
     * Determine if the identifier ends with the specified part string
     *
     * @param string $endsWith      The string that the last part should equal
     * @param bool   $caseSensitive Perform a case-sensitive comparison.
     * Defaults to FALSE
     *
     * @return bool True if the last identifier part equals the specified
     * string
     */
    public function endsWith($endsWith, $caseSensitive = false);

    /**
     * Merge a range of identifier parts into one part
     *
     * @param int      $start The offset of the first identifier part in the
     * range to merge
     * @param int|null $end   The offset of the last identifier part in the
     * range to merge. Without an ending index, all the remaining identifier
     * parts will merge
     *
     * @return $this The current instance of this class for method chaining
     */
    public function mergeRange($start, $end = null);

    /**
     * Add an identifier part to the end of the identifier
     *
     * @param string $part The identifier part to append
     *
     * @return $this The current instance of this class for method chaining
     */
    public function append($part);

    /**
     * An alias for append()
     *
     * @param string $part The identifier part to append
     *
     * @return $this The current instance of this class for method chaining
     */
    public function push($part);

    /**
     * Add an identifier part to the beginning of the identifier
     *
     * @param string $part The identifier part to prepend
     *
     * @return $this The current instance of this class for method chaining
     */
    public function prepend($part);

    /**
     * Insert an identifier part at the specified position in the identifier
     *
     * @param int    $offset The position to insert the part at
     * @param string $part   The identifier part to insert
     *
     * @return $this The current instance of this class for method chaining
     */
    public function insert($offset, $part);

    /**
     * Remove the identifier part at the end of the identifier
     *
     * @return $this The current instance of this class for method chaining
     */
    public function pop();

    /**
     * Remove the identifier part at the beginning of the identifier
     *
     * @return $this The current instance of this class for method chaining
     */
    public function shift();

    /**
     * Remove the identifier part at the specified position
     *
     * @param int $offset The index of the identifier part to remove
     *
     * @return $this The current instance of this class for method chaining
     */
    public function remove($offset);

    /**
     * Replace an identifier part at the specified position
     *
     * @param int    $offset      The index of the identifier part to rename
     * @param string $replacement The value to rename the specified identifier
     * part to
     *
     * @return $this The current instance of this class for method chaining
     *
     * @throws \OutOfBoundsException If no identifier part exists for the
     * specified index
     */
    public function replace($offset, $replacement);

    /**
     * Convert this identifier instance to a DynamicFunction instance
     *
     * @return DynamicFunction The new DynamicFunction instance of the current
     * identifier
     */
    public function toFunction();

    /**
     * Convert this identifier instance to a DynamicMethod instance
     *
     * @return DynamicMethod The new DynamicMethod instance of the current
     * identifier
     */
    public function toMethod();

    /**
     * Get the representation of this object as an array of identifier parts
     *
     * @return array The array of identifier parts
     */
    public function toArray();

    /**
     * Format this identifier as a string by combining any parts using the
     * current output case format
     *
     * @return string The identifier name
     */
    public function __toString();
}
