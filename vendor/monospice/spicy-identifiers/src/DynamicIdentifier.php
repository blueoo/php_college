<?php

namespace Monospice\SpicyIdentifiers;

use InvalidArgumentException;
use Monospice\SpicyIdentifiers\Interfaces\DynamicIdentifierInterface;
use Monospice\SpicyIdentifiers\Tools\CaseFormat;
use Monospice\SpicyIdentifiers\Tools\Formatter;
use OutOfBoundsException;

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
class DynamicIdentifier implements DynamicIdentifierInterface
{
    use MakesDynamicIdentifiers;

    /**
     * The string constant representing the default case to use for this
     * identifier type if we don't explicitly set an output format
     *
     * @var string
     */
    protected static $defaultCase = CaseFormat::CAMEL_CASE;

    /**
     * The array of identifier parts
     *
     * @var array
     */
    protected $identifierParts;

    /**
     * The string constant representing the case to use when outputting
     * or accessing an identifier name
     *
     * @var string
     */
    protected $outputFormat;

    /**
     * Create a new DynamicIdentifier instance. Instead of the constructor, try
     * one of the factory methods
     *
     * @param array $parts The array of identifier parts
     *
     * @see MakesDynamicIdentifiers For the available factory methods
     */
    public function __construct(array $parts)
    {
        $this->identifierParts = $parts;
    }

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
     * @throws InvalidArgumentException If not provided with a valid output
     * case format
     *
     * @see \Monospice\SpicyIdentifiers\Tools\CaseFormat For the available
     * output case formats
     */
    public function setOutputFormat($caseFormat)
    {
        $this->outputFormat = $caseFormat;

        return $this;
    }

    /**
     * Get the case format to use for outputting the identifier or for actions
     * that use the string representation of the identifier
     *
     * @return string The constant representing an output case format
     */
    public function getOutputFormat()
    {
        if ($this->outputFormat === null) {
            return static::$defaultCase;
        }

        return $this->outputFormat;
    }

    /**
     * Get the current parts that make the whole identifier when combined
     *
     * @return array The strings that form the identifier name
     */
    public function parts()
    {
        return $this->identifierParts;
    }

    /**
     * Get the value of the identifier part at the specified offset
     *
     * @param int $offset The index of the identifier part to retrieve, starting
     * from zero
     *
     * @return string|null The name of the corresponding identifier part, or
     * NULL if a part doesn't exist at the specified offset
     */
    public function part($offset)
    {
        if (! isset($this->identifierParts[$offset])) {
            return null;
        }

        return $this->identifierParts[$offset];
    }

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
    public function keys($search = null, $caseSensitive = false)
    {
        if ($search === null) {
            return array_keys($this->identifierParts);
        }

        if ($caseSensitive === true) {
            return array_keys($this->identifierParts, $search);
        }

        $search = strtolower($search);
        $keys = array_keys($this->identifierParts);

        $keys = array_filter($keys, function ($key) use ($search) {
            return strtolower($this->identifierParts[$key]) === $search;
        });

        return array_values($keys);
    }

    /**
     * Get the number of identifier parts in the parsed identifier name
     *
     * @return int The number of identifier parts
     */
    public function getNumParts()
    {
        return count($this->identifierParts);
    }

    /**
     * Get the identifier part at the beginning of the identifier name
     *
     * @return string The value of the first identifier part
     */
    public function first()
    {
        if ($this->getNumParts() < 1) {
            return null;
        }

        return $this->identifierParts[0];
    }

    /**
     * Get the identifier part at the end of the identifier name
     *
     * @return string The value of the last identifier part
     */
    public function last()
    {
        if ($this->getNumParts() < 1) {
            return null;
        }

        return end($this->identifierParts);
    }

    /**
     * Get the string representation of the parsed identifier
     *
     * @return string The identifier parts combined into a single string in
     * the current case format
     */
    public function name()
    {
        return Formatter::format(
            $this->identifierParts,
            $this->getOutputFormat()
        );
    }

    /**
     * Determine if the identifier contains a part at the specified offset
     *
     * @param int $offset The index of the identifier part to check
     *
     * @return bool True if the identifier parts array contains a value at the
     * specified offset
     */
    public function has($offset)
    {
        return isset($this->identifierParts[$offset]);
    }

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
    public function startsWith($startsWith, $caseSensitive = false)
    {
        if (! $caseSensitive) {
            return strtolower($this->first()) === strtolower($startsWith);
        }

        return $this->first() === $startsWith;
    }

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
    public function endsWith($endsWith, $caseSensitive = false)
    {
        if (! $caseSensitive) {
            return strtolower($this->last()) === strtolower($endsWith);
        }

        return $this->last() === $endsWith;
    }

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
    public function mergeRange($start, $end = null)
    {
        if ($end === null) {
            $end = $this->getNumParts() - 1;
        }

        if ($end <= $start) {
            return $this;
        }

        $mergedParts = implode(
            array_slice($this->identifierParts, $start, $end)
        );
        array_splice($this->identifierParts, $start, $end, $mergedParts);

        return $this;
    }

    /**
     * Add an identifier part to the end of the identifier
     *
     * @param string $part The identifier part to append
     *
     * @return $this The current instance of this class for method chaining
     */
    public function append($part)
    {
        $this->identifierParts[] = $part;

        return $this;
    }

    /**
     * An alias for append()
     *
     * @param string $part The identifier part to append
     *
     * @return $this The current instance of this class for method chaining
     */
    public function push($part)
    {
        return $this->append($part);
    }

    /**
     * Add an identifier part to the beginning of the identifier
     *
     * @param string $part The identifier part to prepend
     *
     * @return $this The current instance of this class for method chaining
     */
    public function prepend($part)
    {
        array_unshift($this->identifierParts, $part);

        return $this;
    }

    /**
     * Insert an identifier part at the specified position in the identifier
     *
     * @param int    $offset The position to insert the part at
     * @param string $part   The identifier part to insert
     *
     * @return $this The current instance of this class for method chaining
     */
    public function insert($offset, $part)
    {
        array_splice($this->identifierParts, $offset, 0, $part);

        return $this;
    }

    /**
     * Remove the identifier part at the end of the identifier
     *
     * @return $this The current instance of this class for method chaining
     */
    public function pop()
    {
        array_pop($this->identifierParts);

        return $this;
    }

    /**
     * Remove the identifier part at the beginning of the identifier
     *
     * @return $this The current instance of this class for method chaining
     */
    public function shift()
    {
        array_shift($this->identifierParts);

        return $this;
    }

    /**
     * Remove the identifier part at the specified position
     *
     * @param int $offset The index of the identifier part to remove
     *
     * @return $this The current instance of this class for method chaining
     */
    public function remove($offset)
    {
        array_splice($this->identifierParts, $offset, 1);

        return $this;
    }

    /**
     * Replace an identifier part at the specified position
     *
     * @param int    $offset      The index of the identifier part to rename
     * @param string $replacement The value to rename the specified identifier
     * part to
     *
     * @return $this The current instance of this class for method chaining
     *
     * @throws OutOfBoundsException If no identifier part exists for the
     * specified index
     */
    public function replace($offset, $replacement)
    {
        if (! array_key_exists($offset, $this->identifierParts)) {
            throw new OutOfBoundsException(
                'No identifier part exists at the specified position: ' .
                $offset
            );
        }

        $this->identifierParts[$offset] = $replacement;

        return $this;
    }

    /**
     * Using array access, get the identifier part at the specified position
     *
     * @param int $offset The position of the identifier part to get
     *
     * @return string The identifier part string
     */
    public function offsetGet($offset)
    {
        return $this->part($offset);
    }

    /**
     * Using array access, set the identifier part at the specified position
     *
     * @param int|null $offset The position to set the identifer part at
     * @param string   $part   The value of the identifier part to set
     *
     * @return void
     */
    public function offsetSet($offset, $part)
    {
        if ($offset === null) {
            $this->append($part);
        }

        $this->replace($offset, $part);
    }

    /**
     * Using array access, determine if a part exists at the specified position
     *
     * @param int $offset The position to check
     *
     * @return bool True if a part exists at the specified position
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Using array access, remove the identifier part at the specified position
     *
     * @param int $offset The position to remove an identifier part from
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * Get the number of identifier parts in the parsed identifier name
     *
     * @return int The number of identifer name parts
     */
    public function count()
    {
        return $this->getNumParts();
    }

    /**
     * Convert this identifier instance to a DynamicFunction instance
     *
     * @return DynamicFunction The new DynamicFunction instance of the current
     * identifier
     */
    public function toFunction()
    {
        $function = new DynamicFunction($this->parts());

        return $this->applyOutputFormat($function);
    }

    /**
     * Convert this identifier instance to a DynamicMethod instance
     *
     * @return DynamicMethod The new DynamicMethod instance of the current
     * identifier
     */
    public function toMethod()
    {
        $method = new DynamicMethod($this->parts());

        return $this->applyOutputFormat($method);
    }

    /**
     * Get the representation of this object as an array of identifier parts
     *
     * @return array The array of identifier parts
     */
    public function toArray()
    {
        return $this->parts();
    }

    /**
     * Format this identifier as a string by combining any parts using the
     * current output case format
     *
     * @return string The identifier name
     */
    public function __toString()
    {
        return $this->name();
    }

    /**
     * Applies the current output case to a DynamicIdentifier if already
     * set explicitly
     *
     * @param DynamicIdentifier $identifier The DynamicIdentifier instance to
     * apply the output case to
     *
     * @return DynamicIdentifier The DynamicIdentifier instance with the
     * output case set
     */
    protected function applyOutputFormat(DynamicIdentifier $identifier)
    {
        if ($this->outputFormat !== null) {
            $identifier->setOutputFormat($this->outputFormat);
        }

        return $identifier;
    }
}
