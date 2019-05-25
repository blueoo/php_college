Spicy Identifier Tools
=======

[![Build Status](https://travis-ci.org/monospice/spicy-identifier-tools.svg?branch=master)](https://travis-ci.org/monospice/spicy-identifier-tools)

**An easy way to parse, convert, and format identifier names.**

These tools are helpful when working with dynamic identifier names such
as dynamic methods or when working between different programming languages.

Simple Example
------

```php
$identifierParts = Parser::parseFromUnderscore('an_identifier_name');
// returns array('an', 'identifier', 'name');

echo Formatter::formatCamelCase($identifierParts);
// 'anIdentifierName'
```

Or just:

```php
echo Converter::convert(
    'an_identifier_name',
    CaseFormat::UNDERSCORE,
    CaseFormat::CAMEL_CASE
);
// 'anIdentifierName'
```

Installation
-------

```
$ composer require monospice/spicy-identifier-tools
```

If you're autoloading classes (hopefully):

```php
use Monospice\SpicyIdentifiers\Tools\CaseFormat;
use Monospice\SpicyIdentifiers\Tools\Parser;
use Monospice\SpicyIdentifiers\Tools\Formatter;
use Monospice\SpicyIdentifiers\Tools\Converter;
```

Basic Usage
-------

The package comes with three tools:
- `Parser`: Converts identifier strings into arrays of parts
- `Formatter`: Comverts an array of identifier parts into a formatted string
- `Converter`: Parses and formats an identifier string in one step

**Parser**
```php
Parser::parseFromCamelCase('anIdentifier');     // array('an', 'Identifier');
Parser::parseFromUnderscore('an_identifier');   // array('an', 'identifier');
Parser::parseFromHyphen('an-identifier');       // array('an', 'identifier');

// or use the generic method:
Parser::parse('anIdentifier', CaseFormat::CAMEL_CASE);
// array('an', 'Identifier');
```

*Note*: Although PHP doesn't support hyphens in identifier names, the hyphen
methods may be useful when working between other languages that do, like
HTML/CSS or Lisp (gasp!).

**Formatter**
```php
$parts = array('an', 'identifier');

Formatter::formatUppercase($parts);         // 'ANIDENTIFIER'
Formatter::formatLowercase($parts);         // 'anidentifier'
Formatter::formatCamelCase($parts);         // 'anIdentifier'
Formatter::formatUpperCamelCase($parts);    // 'AnIdentifier'
Formatter::formatUnderscore($parts);        // 'an_identifier'
Formatter::formatUpperUnderscore($parts);   // 'An_Identifier'
Formatter::formatCapsUnderscore($parts);    // 'AN_IDENTIFIER'
Formatter::formatHyphen($parts);            // 'an-identifier'
Formatter::formatUpperHyphen($parts);       // 'An-Identifier'
Formatter::formatCapsHyphen($parts);        // 'AN-IDENTIFIER'

// or use the generic method:
Formatter::format($parts, CaseFormat::CAPS_UNDERSCORE); // AN_IDENTIFIER
```

**Converter**

At this time, the `Converter` class only provides a generic method:

```php
// Converter::convert($partsArray, $inputFormat, $outputFormat);

Converter::convert(
    'anIdentifierName',
    CaseFormat::CAMEL_CASE,
    CaseFormat::UPPER_CAMEL_CASE
);
// 'AnIdentifierName'
```

Case Formats
-------

The `CaseFormat` class contains constants that represent each case.

This package supports the following "cases" for identifiers:

Case Format              | Constant Name                  | Example
------------------------ | ------------------------------ | ------------------
Uppercase                | UPPERCASE                      | ANIDENTIFIER
Lowercase                | LOWERCASE                      | anidentifier
Camel Case               | CAMEL_CASE                     | anIdentifier
Upper Camel Case         | UPPER_CAMEL_CASE or STUDLY_CAPS| AnIdentifier
Camel Case with Acronyms | CAMEL_CASE_WITH_ACRONYMS       | anIdentifierACRNM
Upper CC with Acronyms   | UPPER_CAMEL_CASE_WITH_ACRONYMS | AnIdentifierACRNM
Underscore (snake case)  | UNDERSCORE or SNAKE_CASE       | an_identifier
Upper Underscore         | UPPER_UNDERSCORE               | An_Identifier
Capitalized Underscore   | CAPS_UNDERSCORE                | AN_IDENTIFIER
Underscore with Acronyms | UNDERSCORE_WITH_ACRONYMS       | an_identifier_ACRNM
Upper US with Acronyms   | UPPER_UNDERSCORE_WITH_ACRONYMS | An_Identifier_ACRNM
Hyphenated               | HYPHEN                         | an-identifier
Upper Hyphenated         | UPPER_HYPHEN                   | An-Identifier
Capitalized Hyphenated   | CAPS_HYPHEN                    | AN-IDENTIFIER
Hyphenated  Acronyms     | HYPHEN_WITH_ACRONYMS           | an-identifier-ACRNM
Upper Hy. with Acronyms  | UPPER_HYPHEN_WITH_ACRONYMS     | An-Identifier-ACRNM

*Note*: Because the `Parser` class does not perform formatting, when using the
`::parse()` method, one must choose a "base" case to parse from, such as
`CAMEL_CASE`, not `UPPER_CAMEL_CASE` or `CAMEL_CASE_WITH_ACRONYMS`.

Acronyms in Identifier Names
-------
Sometimes identifier names contain acronyms, such as JavaScript's
`XMLHttpRequest`. The `Parser` class preserves these acronyms:

```php
$parts = Parser::parseFromCamelCase('XMLHttpRequest');
// array('XML', 'Http', 'Request');
```

However, the `Formatter` and `Converter` classes will not preserve these
acronyms unless one chooses an output format with acronyms:

```php
Formatter::formatCamelCase($parts);             // 'xmlHttpRequest'
Formatter::formatCamelCaseWithAcronyms($parts); // 'XMLHttpRequest'
```

This behavior provides flexibility when converting or normalizing identifier
names.

Identifiers with Mixed Case
-------
Although mixed case identifiers are not recommended in practice, one may use
the `Parser` to parse identifiers that contain multiple cases:

```php
// Parser::parseFromMixedCase($identiferString, $arrayOfCases);

Parser::parseFromMixedCase('aMixed_case-identifier', [
    CaseFormat::CAMEL_CASE,
    CaseFormat::UNDERSCORE,
    CaseFormat::HYPHEN,
]);
// array('a', 'Mixed', 'case', 'identifier');
```

The package does not provide support to output identifiers in a mixed format.

Extended ASCII Identifiers (Experimental)
-------

PHP supports extended ASCII characters in identifier names. For example, the
character `ä` in:

```php
// From the php.net manual:
$täyte = 'mansikka';    // valid; 'ä' is (Extended) ASCII 228.
```

When parsing identifiers by underscore or hyphen, these characters have no
effect. However, camel case identifiers may include words that are delimited
by extended ASCII characters, such as `änÏdentifierNáme`.

The Spicy Identifier Tools package provides an **experimental** method to parse
these identifiers:

```php
Parser::parseFromCamelCaseExtended('änÏdentifierNáme');
// array('än', 'Ïdentifier', 'Náme');
```

The consistency of this method depends on the character encoding of the source
files and the environment language and encoding settings. As a best practice,
one should avoid using extended ASCII characters in identifier names.

For more information, visit the PHP Manual:
http://php.net/manual/en/language.variables.basics.php

Testing
-------

The Spicy Identifier Tools package uses PHPUnit to test input variations and
PHPSpec for object behavior.

``` bash
$ phpunit
$ vendor/bin/phpspec run
```

License
-------

The MIT License (MIT). Please see the [LICENSE File](LICENSE) for more
information.

[PSR-2]: http://www.php-fig.org/psr/psr-2/
[PSR-4]: http://www.php-fig.org/psr/psr-4/
