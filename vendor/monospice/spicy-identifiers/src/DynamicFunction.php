<?php

namespace Monospice\SpicyIdentifiers;

use BadFunctionCallException;
use Monospice\SpicyIdentifiers\Interfaces\DynamicFunctionInterface;
use Monospice\SpicyIdentifiers\Tools\CaseFormat;

/**
 * Parses and manipulates function names. Useful when working with dynamic
 * functions
 *
 * For class methods, use the DynamicMethod class instead.
 *
 * @category Package
 * @package  Monospice\SpicyIdentifiers
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     https://github.com/monospice/spicy-identifiers
 */
class DynamicFunction extends DynamicIdentifier implements
    DynamicFunctionInterface
{
    /**
     * The string constant representing the default case to use for this
     * identifier type if we did not explicitly set an output case format
     *
     * @var string
     */
    protected static $defaultCase = CaseFormat::UNDERSCORE;

    /**
     * Check if the function represented by the current instance exists
     *
     * @return bool True if the function exists
     *
     * @see  function_exists()
     * @link http://php.net/manual/en/function.function-exists.php
     */
    public function exists()
    {
        return function_exists($this->name());
    }

    /**
     * Call the function represented by the current instance
     *
     * @param array $arguments The arguments to pass to the called function
     *
     * @return mixed The return value of the called function
     *
     * @see  call_user_func_array()
     * @link http://php.net/manual/en/function.call-user-func-array.php
     */
    public function call(array $arguments = [ ])
    {
        return call_user_func_array($this->name(), $arguments);
    }

    /**
     * Throw a BadFunctionCallException. The default exception message assumes
     * that we throw the exception because it doesn't exist
     *
     * @param string|null $message The customizable exception message
     *
     * @return void
     *
     * @throws BadFunctionCallException With the given message or a default
     * message that assumes that the function doesn't exist
     */
    public function throwException($message = null)
    {
        if ($message === null) {
            $message = 'The function [' . $this->name() . '] does not exist.';
        }

        throw new BadFunctionCallException($message);
    }

    /**
     * Throw a BadFunctionCallException if the function represented by this
     * instance does not exist
     *
     * @param string|null $message The customizable exception message
     *
     * @return $this The current instance of this class for method chaining
     *
     * @throws BadFunctionCallException If the function represented by this
     * instance does not exist
     */
    public function throwExceptionIfMissing($message = null)
    {
        if (! $this->exists()) {
            $this->throwException($message);
        }

        return $this;
    }
}
