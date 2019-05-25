<?php

namespace Monospice\SpicyIdentifiers;

use BadMethodCallException;
use Monospice\SpicyIdentifiers\Interfaces\DynamicMethodInterface;
use Monospice\SpicyIdentifiers\Tools\CaseFormat;

/**
 * Parses and manipulates class method names. Useful when working with dynamic
 * methods
 *
 * For standard functions, use the DynamicFunction class instead.
 *
 * @category Package
 * @package  Monospice\SpicyIdentifiers
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     https://github.com/monospice/spicy-identifiers
 */
class DynamicMethod extends DynamicIdentifier implements DynamicMethodInterface
{
    /**
     * The string constant representing the default case to use for this
     * identifier type if we did not explicitly set an output case format
     *
     * @var string
     */
    protected static $defaultCase = CaseFormat::CAMEL_CASE;

    /**
     * Check if the method represented by the current instance exists in the
     * given context
     *
     * @param object|string $context The context to check in
     *
     * @return bool True if the method exists in the given context
     *
     * @see  method_exists()
     * @link http://php.net/manual/en/function.method-exists.php
     */
    public function existsOn($context)
    {
        return method_exists($context, $this->name());
    }

    /**
     * Call the method represented by the the current instance in the specified
     * context from the public scope
     *
     * @param object|string $context   The context in which to call the method
     * @param array         $arguments The arguments to pass to the called
     * method
     *
     * @return mixed The return value of the called method
     *
     * @see  call_user_func_array()
     * @link http://php.net/manual/en/function.call-user-func-array.php
     */
    public function callOn($context, array $arguments = [ ])
    {
        return call_user_func_array([ $context, $this->name() ], $arguments);
    }

    /**
     * Call the method represented by the the current instance in the specified
     * context from the scope of that context
     *
     * We use this method instead of self::callOn() when we need to dynamically
     * call a private or protected method that this class cannot otherwise
     * access, such as if we're using a DynamicMethod instance in a class that
     * needs to call its own private methods. As a best practice, avoid using
     * this method to call private or protected methods from scopes that cannot
     * normally access those methods
     *
     * @param object|string $context   The context in which to call the method
     * @param array         $arguments The arguments to pass to the called
     * method
     *
     * @return mixed The return value of the called method
     *
     * @see  call_user_func_array()
     * @link http://php.net/manual/en/function.call-user-func-array.php
     */
    public function callFromScopeOn($context, array $arguments = [ ])
    {
        $scopeWrapper = function ($context, $methodName, array $arguments) {
            return call_user_func_array([ $context, $methodName ], $arguments);
        };

        if (is_object($context)) {
            $scopeWrapper = $scopeWrapper->bindTo($context, $context);
        } else {
            $scopeWrapper = $scopeWrapper->bindTo(null, $context);
        }

        return $scopeWrapper($context, $this->name(), $arguments);
    }

    /**
     * DEPRECATED: Use self::callFromScopeOn() instead
     *
     * @param object|string $context   The context in which to call the method
     * @param array         $arguments The arguments to pass to the called
     * method
     *
     * @return mixed The return value of the invoked method
     *
     * @deprecated We replaced this method with self::callFromScopeOn() for
     * cases when we need to call a private or protected method from a scope
     * that this class cannot normally access
     *
     * @see self::callFromScopeOn()
     */
    public function invokeOn($context, array $arguments = [ ])
    {
        return $this->callFromScopeOn($context, $arguments);
    }

    /**
     * Forward the call to the static method represented by the current instance
     * in the specified context for late static binding
     *
     * @param object|string $context   The context in which to call the static
     * method
     * @param array         $arguments The arguments to pass to the called
     * method
     *
     * @return mixed The return value of the called method
     *
     * @see  forward_static_call_array()
     * @link http://php.net/manual/en/function.forward-static-call-array.php
     */
    public function forwardStaticCallTo($context, array $arguments = [ ])
    {
        $methodName = $this->name();

        return forward_static_call_array([ $context, $methodName ], $arguments);
    }

    /**
     * Throw a BadMethodCallException. The default exception message assumes
     * that the exception is thrown because the method doesn't exist
     *
     * @param string|null $message The customizable exception message
     *
     * @return void
     *
     * @throws BadMethodCallException With the given message or a default
     * message that assumes that the method doesn't exist
     */
    public function throwException($message = null)
    {
        if ($message === null) {
            $message = 'The method [' . $this->name() . '] does not exist.';
        }

        throw new BadMethodCallException($message);
    }

    /**
     * Throw a BadMethodCallException if the method represented by this
     * instance does not exist
     *
     * @param object|string $context The context to check in for method
     * existance
     * @param string|null   $message The customizable exception message
     *
     * @return $this The current instance of this class for method chaining
     *
     * @throws BadMethodCallException If the method represented by this
     * instance does not exist
     */
    public function throwExceptionIfMissingOn($context, $message = null)
    {
        if ($message === null) {
            $message = 'The method [' . $this->name() . '] does not exist on ['
                . static::getClassName($context) . '].';
        }

        if (! $this->existsOn($context)) {
            $this->throwException($message);
        }

        return $this;
    }

    /**
     * Get the class name of the provided context
     *
     * @param object|string $context The context to get the class name of
     *
     * @return string The class name of the provided context
     */
    protected static function getClassName($context)
    {
        if (is_string($context)) {
            return $context;
        }

        return get_class($context);
    }
}
