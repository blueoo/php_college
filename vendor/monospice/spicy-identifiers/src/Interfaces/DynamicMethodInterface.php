<?php

namespace Monospice\SpicyIdentifiers\Interfaces;

/**
 * Parses and manipulates class method names. Useful when working with dynamic
 * methods
 *
 * For standard functions, use an instance of DynamicFunctionInterface instead.
 *
 * @category Package
 * @package  Monospice\SpicyIdentifiers
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     https://github.com/monospice/spicy-identifiers
 */
interface DynamicMethodInterface extends DynamicIdentifierInterface
{
    /**
     * Check if the method represented by the current instance exists in the
     * specified context
     *
     * @param object|string $context The context to check in
     *
     * @return bool True if the method exists in the given context
     */
    public function existsOn($context);

    /**
     * Call the method represented by the the current instance in the specified
     * context
     *
     * @param object|string $context   The context in which to call the method
     * @param array         $arguments The arguments to pass to the called
     * method
     *
     * @return mixed The return value of the called method
     */
    public function callOn($context, array $arguments = [ ]);

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
     */
    public function callFromScopeOn($context, array $arguments = [ ]);

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
     */
    public function invokeOn($context, array $arguments = [ ]);

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
     */
    public function forwardStaticCallTo($context, array $arguments = [ ]);

    /**
     * Throw a BadMethodCallException. The default exception message assumes
     * that the exception is thrown because the method doesn't exist
     *
     * @param string|null $message The customizable exception message
     *
     * @return void
     *
     * @throws \BadMethodCallException With the given message or a default
     * message that assumes that the method doesn't exist
     */
    public function throwException($message = null);

    /**
     * Throw a BadMethodCallException if the method represented by this
     * instance does not exist
     *
     * @param object|string $context The context to check in for method
     * existance
     * @param string|null   $message The customizable exception message
     *
     * @return void
     *
     * @throws \BadMethodCallException If the method represented by this
     * instance does not exist
     */
    public function throwExceptionIfMissingOn($context, $message = null);
}
