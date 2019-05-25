Spicy Identifiers
=================

[![Build Status](https://travis-ci.org/monospice/spicy-identifiers.svg?branch=1.0.x)](https://travis-ci.org/monospice/spicy-identifiers)

**An easy way to parse and manipulate identifier names, such as dynamic method
names.**

This package improves the experience of working with dynamic identifier names
such as dynamically accessed methods and variables/properties. It also eases
conversion between identifer case formats like `camelCase` and `snake_case`.

For instance, imagine that we have a configuration file that contains an array
of configuration directives and a class that consumes those directives. If we
write the configuration keys in `snake_case`, but the class uses `camelCase`
to define corresponding setter methods, we can use this package to bridge the
two more easily. See below for an example.

**Note:** The current stable version only includes support for working with
dynamic methods and functions. Support for classes, variables, and properties
is under development.

Simple Examples
---------------

Developers often [use PHP's magic methods to dynamically overload class
methods](http://php.net/manual/en/language.oop5.overloading.php#object.call)
for more flexible functionality. This package can simplify the work needed
to handle the dynamic method call:

```php
<?php

// Call a dynamic method:
$someClass->callSomeDynamicMethod('some argument');

// The dynamic method call is handled by the __call() magic method of a class.
// $methodCalled is the name of the dynamic method: "callSomeDynamicMethod"
public function __call($methodCalled, array $arguments)
{
    // Use Spicy Identifiers to work with the dynamic method
    $method = DynamicMethod::parse($methodCalled);

    // Check if the method name starts and ends with certain strings
    if ($method->startsWith('call') && $method->endsWith('Method')) {
        $method->replace(0, 'get')->replace(3, 'Variable');
        // The dynamic method name is now "getSomeDynamicVariable"
    }

    // Alert the developer if they called a method that doesn't exist
    $method->throwExceptionIfMissingOn($this);

    // Check that the method includes the word "Dynamic" in the name,
    // then call the method represented by the name and return that value
    if ($method->has(2) && $method[2] === 'Dynamic') {
        return $method->callOn($this, $arguments);
    }
}
```

Consider another example with a class that consumes configuration directives
stored as an array. The configuration keys are written in `snake_case`, but
the class uses `camelCase` to define corresponding setter methods. This package
makes it easy to load the configuration:

```php
<?php

$config = [
    'config_directive_1' => 'some value',
    'config_directive_2' => 'some value',
    ...
];

class UsesConfig
{
    public function __construct(array $config)
    {
        foreach ($config as $option => $value) {
            DynamicMethod::parse($option)
                ->prepend('set')
                ->throwExceptionIfMissingOn($this, 'Invalid option: ' . $option)
                ->callOn($this, [ $value ]); // calls setConfigDirectiveN()
        }
    }

    public function setConfigDirective1($value) { ... }

    public function setConfigDirective2($value) { ... }

    ...
}
```

Installation
------------

```
$ composer require monospice/spicy-identifiers
```

We'll need to make sure to import the classes we want to use:

```php
use Monospice\SpicyIdentifiers\DynamicVariable;
use Monospice\SpicyIdentifiers\DynamicMethod;
use Monospice\SpicyIdentifiers\DynamicFunction;
use Monospice\SpicyIdentifiers\DynamicClass;
use Monospice\SpicyIdentifiers\Tools\CaseFormat;
```

This package automatically installs the related [Spicy Identifier Tools][tools]
package of classes in the namespace `Monospice\SpicyIdentifiers\Tools`.

Types of Identifiers
--------------------

This package provides different classes for working with various types of
identifiers:

- `DynamicIdentifier`: A generic class that manipulates identifier names but
provides no additional functionality
- `DynamicMethod`: Provides methods and defaults that expedite the process of
working with class methods
- `DynamicFunction`: Provides methods and defaults that expedite the process of
working with standard functions
- `DynamicVariable`: Provides methods and defaults that expedite the process of
working with variables
- `DynamicClass`: Provides methods and defaults that expedite the process of
working with classes

Parsing Identifer Names
-----------------------

To begin working with an identifier string, such as a method name, use one of
the package's factory methods to parse it into the object:

```php
$method = DynamicMethod::parse('aMethodName');
$function = DynamicFunction::parse('a_function_name');
$variable = DynamicVariable::parse('aVariableName');
$class = DynamicClass::parse('AClassName');

$method->parts(); // array('a', 'Method', 'Name')
```

The `::parse()` factory method uses the default case format for the identifer
type represented by each of the package's classes. To parse an identifier in a
specific format, use the respective parsing method:

```php
$method = DynamicMethod::parseFromUnderscore('a_method_name');
$method->parts(); // array('a', 'method', 'name')
```

In addition to `::parse()`, we can use the following factory methods from any
of the `DynamicIdentifier` subclasses in this package to parse identifiers in
a specific format:

- **parseFromCamelCase()** - Identifiers like: `anIdentifierName`
- **parseFromCamelCaseExtended()** - Identifiers like: `änÏdentifierNáme`
- **parseFromUnderscore()** - Identifiers like: `an_identifier_name`
- **parseFromSnakeCase()** - Alias for **parseFromUnderscore()**
- **parseFromHyphen()** - Identifiers like:  `an-identifier-name`
- **parseFromMixedCase()** - Identifiers like: `aMixed_case-identifier`

For more information about identifier case formats and mixed-case or extended ASCII
identifiers, [see below](#identifier-case-formats).

Loading an Identifier
---------------------

Sometimes we may wish to use the dynamic features of the classes in this
package, but we don't need to parse the identifier string into its component
parts. In these cases, we can avoid invoking the parser by using the `::from()`
factory method to simply create an instance for the identifier string to improve
performance.

```php
$method = DynamicMethod::from('aMethodName');
$method->parts(); // array('aMethodName')

$returnValue = $method->callOn($this);
```

Similarly, in some cases we may already know or have the set of identifier parts
that we'd like to use. We can call the `::fromParts()` factory method to create
an instance for the represented identifier:

```php
$method = DynamicMethod::fromParts(['a', 'method', 'name']);
$method->parts(); // array('a', 'method', 'name')

$returnValue = $method->callOn($this);
```

Identifier Manipulation
-----------------------

After parsing an identifier, we can use this package to manipulate the parts.
Let's use this `DynamicIdentifier` instance for the following examples:

```php
$identifier = DynamicIdentifier::parse('anIdentifierName');
```

At any time, we can retrieve the current identifier name:

**name()** - get the string representation of the entire identifier name

```php
$identifier->name();                    // "anIdentifierName"
```

Alternatively, we can cast the dynamic identifer instance to a string:

```php
echo $identifier;                       // "anIdentifierName"
```

### Getting Identifer Part Data

**parts()** - get an array of identifier part names

```php
$identifier->parts();                   // ['an', 'Identifier', 'Name']
$identifier->toArray();                 // an alias for parts()
```

**part()** - get the string value of the specified identifier part

```php
$identifer->part(1);                    // "Identifier"
```

Alternatively, use array access to get the value:

```php
$identifier[1];                         // "Identifier"
```

Note that the array of parts is zero-based, so the first part corresponds to
the index `0`.

**first()** - get the value of the first identifier part

```php
$identifier->first();                   // "an"
```

**last()** - get the value of the last identifier part

```php
$identifier->last();                    // "Name"
```

**keys()** - get an array of identifier part indices

```php
$identifier->keys();                    // [0, 1, 2]
```

We can pass a string to the `keys()` method to get an array of indices with
parts that match the value:

```php
$identifier->keys('Name');              // [2]
```

Note that `keys()` performs a case-insensitive comparison by default. To match
the exact case, set the second parameter to `true`:

```php
$identifier->keys('NAME', true);        // [ ]
```

**getNumParts()** - get the number of identifier parts

```php
$identifier->getNumParts();             // 3
$identifier->count();                   // an alias for getNumParts()
```

Alternatively, use PHP's count() function to get the number of identifier parts:

```php
count($identifier);                     // 3
```

### Checking Identifer Parts

**has()** - check if the identifier contains a part at the specified index

```php
$identifier->has(1);                    // true
```

One may use array access for the above as well:

```php
isset($identifier[1]);                  // true
```

**startsWith()** - check if the identifier starts with the specified string

```php
$identifier->startsWith('an');          // true
$identifier->startsWith('identifier');  // false
```

**endsWith()** - check if the identifier ends with the specified string

```php
$identifier->endsWith('name');          // true
$identifier->endsWith('identifier');    // false
```

Note that `startsWith()` and `endsWith()` perform case-insensitive comparisons
by default. To match the exact case, set the second parameter to `true`:

```php
$identifier->endsWith('NAME', true);    // false
```

### Adding Parts

**append()** - add a part to the end of the identifier

```php
$identifier->append('last');            // "anIdentifierNameLast"
$identifier->push('last');              // alias for append()
```

Alternatively, use array access to push a part to the end of the identifier:

```php
$identifier[] = 'last';
```

**prepend()** - add a part to the beginning of the identifier

```php
$identifier->prepend('first');          // "firstAnIdentifierName"
```

**insert()** - add a part to the specified position in the identifier

```php
$identifier->insert(1, 'insert');       // "anInsertIdentifierName"
```

### Removing Parts

**pop()** - remove a part from the end of the identifier

```php
$identifier->pop();                     // "anIdentifier"
```

**shift()** - remove a part from the beginning of the identifier

```php
$identifier->shift();                   // "identifierName"
```

**remove()** - remove a part at the specified position of the identifier

```php
$identifier->remove(1);                 // "anName"
```

### Replacing Parts

**replace()** - replace a part at the specified position of the identifier

```php
$identifier->replace(2, 'String');      // "anIdentifierString"
```

Alternatively, use array access to replace a part at the specified index:

```php
$identifier[2] = 'String';              // "anIdentifierString"
```

### Merging Parts

Merging parts doesn't change the output string, but combines parts of the
internal array. This is useful for other operations.

**mergeRange()** - combine identifier parts between the specified positions

```php
$identifier = DynamicIdentifier::parse('anIdentifierName');

echo $identifier->mergeRange(1, 2);     // "anIdentifierName"
$identifier->parts();                   // array(
                                        //     0 => "an",
                                        //     1 => "IdentifierName"
                                        // )
```

If one does not specify an ending position, any remaining parts after the
starting position will be merged.

```php
$identifier->mergeRange(0)->parts();    // array(
                                        //     0 => "anIdentifierName"
                                        // )
```

Dynamic Methods
---------------

The `DynamicMethod` class adds functionality for working with an underlying
class method that corresponds to the parsed identifier name.

```php
$method = DynamicMethod::parse('someMethod');
```

**existsOn()** - check if the represented method exists in the given class
context

```php
$method->existsOn('Namespace\SomeClass');
$method->existsOn(SomeClass::class);
$method->existsOn($someInstance);
$method->existsOn($this);
```

**callOn()** - call the method represented by the parsed method name in the
given context

```php
$returnValue = $method->callOn($someInstance);
$returnValue = $method->callOn($someInstance, ['arg1', 'arg2']);
$returnValue = $method->callOn($this, ['arg1', 'arg2']);

// Static Methods
$returnValue = $method->callOn('Namespace\SomeClass');
$returnValue = $method->callOn('Namespace\SomeClass', ['arg1']);
```

**callFromScopeOn()** - call the method represented by the parsed method name in
the given context from the scope of that context

This method is similar to `callOn()`, but it permits access to private and
protected methods that the `DynamicMethod` instance cannot call directly.
`callFromScopeOn()` is intented for cases where `DynamicMethod` is used inside
a class that could otherwise normally access its private and protected members
directly. One should consider this use carefully before choosing this method,
and always use `callOn()` for public methods.

```php
$returnValue = $method->callFromScopeOn($someInstance);
$returnValue = $method->callFromScopeOn($this, ['arg1', 'arg2']);

// Static Methods
$returnValue = $method->callFromScopeOn('Namespace\SomeClass');
$returnValue = $method->callFromScopeOn('Namespace\SomeClass', ['arg1']);
```

**forwardStaticCallTo()** - forward the call to the static method represented
by the parsed method name in the given context for late static binding

```php
$returnValue = $method->forwardStaticCallTo('Namespace\SomeClass');
$returnValue = $method->forwardStaticCallTo('SomeClass', ['arg1', 'arg2']);
```

**throwException()** - throw a `BadMethodCallException`. The default exception
message assumes that the exception is thrown becuase the method does not exist

```php
$method->throwException();
```

One may specify the exception message in the first parameter:

```php
$method->throwException('A custom exception message');
```

**throwExceptionIfMissingOn()** - throw a `BadMethodCallException` if the method
does not exist in the given context

```php
$method->throwExceptionIfMissingOn($someObject);
$method->throwExceptionIfMissingOn('Namespace\SomeClass');
```

One may specify the exception message in the second parameter:

```php
$method->throwExceptionIfMissingOn($someObject, 'A custom exception message');
```

Dynamic Functions
-----------------

The `DynamicFunction` class adds functionality for working with an underlying
standard function that corresponds to the parsed identifier name.

```php
$function = DynamicFunction::parse('some_function');
```

**exists()** - check if the represented function exists

```php
$function->exists();
```

**call()** - call the function represented by the parsed function name

```php
$returnValue = $function->call();
$returnValue = $function->call(['arg1']);
```

**throwException()** - throw a `BadFunctionCallException`. The default exception
message assumes that the exception is thrown becuase the function does not exist

```php
$function->throwException();
```

One may specify the exception message in the first parameter:

```php
$function->throwException('A custom exception message');
```

**throwExceptionIfMissing()** - throw a `BadFunctionCallException` if the
function does not exist

```php
$function->throwExceptionIfMissing();
```

One may specify the exception message in the first parameter:

```php
$function->throwExceptionIfMissing('A custom exception message');
```

Changing Dynamic Identifier Types
---------------------------------

We can obtain a particular identifer type from any of the Dynamic Identifier
classes in this package. For example, a developer can get the `DynamicVariable`
representation of a `DynamicMethod` with the same identifier name, but with
methods specific to variables:

```php
$method = DynamicMethod::parse('anIdentifierName');
$method->name();                   // 'anIdentifierName'

$variable = $method->toVariable();
get_class($variable);              // Monospice\SpicyIdentifiers\DynamicVariable
$variable->name();                 // 'anIdentifierName'
$variable->value();                // the value of the corresponding variable
```

Note that this functionality does not cast the original object, but returns a
new instance of the corresponding class. Because of this, remember to assign
the returned object to a variable if you plan to use the converted instance
later in the code. This design encourages proper variable names for each type.

The available conversions are:

```php
$method = $identifier->toMethod();      // DynamicMethod
$variable = $identifier->toVariable();  // DynamicVariable
$class = $identifier->toClass();        // DynamicClass
$function = $identifier->toFunction();  // DynamicFunction
```

Method Chaining
---------------

Methods that do not return an output value can be chained:

```php
$returnValue = DynamicMethod::parse('aDynamicMethod')
    ->append('last')
    ->mergeRange(1, 2)
    ->callOn($this);
```

Identifier Case Formats
-----------------------

Each class uses a default case format to parse and output identifiers. These
formats are constants set on the `Tools\CaseFormat` class.

For more information about the supported case formats, see the
[Spicy Identifier Tools][tools] package which this package includes
automatically.

Identifier Class         | Default Case Format            | Example
------------------------ | ------------------------------ | ------------------
`DynamicVariable`        | `CaseFormat::CAMEL_CASE`       | variableName
`DynamicMethod`          | `CaseFormat::CAMEL_CASE`       | methodName
`DynamicClass`           | `CaseFormat::UPPER_CAMEL_CASE` | ClassName
`DynamicFunction`        | `CaseFormat::UNDERSCORE`       | function_name
`DynamicIdentifier`      | `CaseFormat::CAMEL_CASE`       | identifierName

To override this default formatting, parse the identifier using one of the
dedicated methods and/or set the output formatting explicitly:

```php
// parse and output with the default case format (camel case)
$identifier = DynamicIdentifier::parse('identifierName');

// parse with an explicit case format, output with the default format
$identifier = DynamicIdentifier::parseFromUnderscore('identifier_name');

// parse with an explicit format, and set an explicit output format
$identifier = DynamicIdentifier::parseFromUnderscore('identifier_name')
    ->setOutputFormat(CaseFormat::UPPER_CAMEL_CASE);
```

Acronyms in Identifier Names
----------------------------

Sometimes identifier names contain acronyms, such as `XML` in JavaScript's
`XMLHttpRequest`. The parsing methods preserve these acronyms:

```php
$method = DynamicMethod::parse('XMLHttpRequest')
$method->parts();   // array('XML', 'Http', 'Request');
```

However, the output methods will not preserve these acronyms unless we set
an output format that preserves acronyms:

```php
$method->name();    // "xmlHttpRequest"

$method
    ->setOutputFormat(CaseFormat::CAMEL_CASE_WITH_ACRONYMS)
    ->name();       // "XMLHttpRequest"
```

This behavior provides flexibility when converting or normalizing identifier
names.

Identifiers with Mixed Case Formats
-----------------------------------

Although mixed case identifiers are not recommended in practice, one may use
the `::parseFromMixedCase()` method to parse identifiers that contain multiple
cases:

```php
// parseFromMixedCase($identiferString, $arrayOfCaseFormatsToParse);

DynamicIdentifier::parseFromMixedCase('aMixed_case-identifier', [
    CaseFormat::CAMEL_CASE,
    CaseFormat::UNDERSCORE,
    CaseFormat::HYPHEN,
])
    ->parts(); // array('a', 'Mixed', 'case', 'identifier');
```

The package does not provide support to output identifiers in a mixed format.
Any output methods will format the output string using the default format
unless explicitly specified (see preceding section).

Extended ASCII Identifiers (Experimental)
-----------------------------------------

PHP supports extended ASCII characters in identifier names. For example, the
character `ä` in:

```php
// From the php.net manual:
$täyte = 'mansikka';    // valid; 'ä' is (Extended) ASCII 228.
```

When parsing identifiers by underscore or hyphen, these characters have no
effect. However, camel case identifiers may include words that are delimited
by extended ASCII characters, such as `änÏdentifierNáme`.

The Spicy Identifiers package provides an **experimental** method to parse
these identifiers:

```php
DynamicIdentifier::parseFromCamelCaseExtended('änÏdentifierNáme')
    ->parts(); // array('än', 'Ïdentifier', 'Náme');
```

The consistency of this method depends on the character encoding of the source
files and the environment language and encoding settings. As a best practice,
one should avoid using extended ASCII characters in identifier names.

For more information, visit the PHP Manual:
http://php.net/manual/en/language.variables.basics.php

Testing
-------

The Spicy Identifiers package uses PHPSpec to test object behavior.

```bash
$ vendor/bin/phpspec run
```

License
-------

The MIT License (MIT). Please see the [LICENSE File](LICENSE) for more
information.

[tools]: https://github.com/monospice/spicy-identifier-tools "Spicy Identifier Tools"
