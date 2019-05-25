<?php

namespace Monospice\SpicyIdentifiers\Interfaces;

/**
 * Parses and manipulates function names. Useful when working with dynamic
 * functions
 *
 * For class methods, use an instance of DynamicMethodInterface instead.
 *
 * @category Package
 * @package  Monospice\SpicyIdentifiers
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     https://github.com/monospice/spicy-identifiers
 */
interface DynamicFunctionInterface extends DynamicIdentifierInterface
{
    /**
     * Check if the function represented by the current instance exists
     *
     * @return bool True if the function exists
     */
    public function exists();

    /**
     * Call the function represented by the current instance
     *
     * @param array $arguments The arguments to pass to the called function
     *
     * @return mixed The return value of the called function
     */
    public function call(array $arguments = []);

    /**
     * Throw a BadFunctionCallException. The default exception message assumes
     * that we throw the exception because it doesn't exist
     *
     * @param string|null $message The customizable exception message
     *
     * @return void
     *
     * @throws \BadFunctionCallException With the given message or a default
     * message that assumes that the function doesn't exist
     */
    public function throwException($message = null);

    /**
     * Throw a BadFunctionCallException if the function represented by this
     * instance does not exist
     *
     * @param string|null $message The customizable exception message
     *
     * @return $this The current instance of this class for method chaining
     *
     * @throws \BadFunctionCallException If the function represented by this
     * instance does not exist
     */
    public function throwExceptionIfMissing($message = null);
}
