<?php

namespace Monospice\SpicyIdentifiers\Tools\Tests;

use PHPUnit_Framework_TestCase;

use Monospice\SpicyIdentifiers\Tools\CaseFormat;
use Monospice\SpicyIdentifiers\Tools\Converter;

class ConverterTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    /**
     * Returns a set of test data containing identifers with all caps
     * for use with tests that don't preserve acronyms
     *
     * @return array The array of identifiers and corresponding formats
     */
    public function identifierProvider()
    {
        return [
            ['anIdentifier1ACRNM', CaseFormat::CAMEL_CASE],
            ['AnIdentifier1ACRNM', CaseFormat::CAMEL_CASE],
            ['an_identifier1_ACRNM', CaseFormat::UNDERSCORE],
            ['An_Identifier1_ACRNM', CaseFormat::UNDERSCORE],
            ['an-identifier1-ACRNM', CaseFormat::HYPHEN],
            ['An-Identifier1-ACRNM', CaseFormat::HYPHEN],
        ];
    }

    /**
     * Returns a set of test data containing identifers without all caps
     * for use with tests that preserve acronyms
     *
     * @return array The array of identifiers and corresponding formats
     */
    public function identifierWithCapsProvider()
    {
        return array_merge($this->identifierProvider(), [
            ['AN_IDENTIFIER1_ACRNM', CaseFormat::UNDERSCORE],
            ['AN-IDENTIFIER1-ACRNM', CaseFormat::HYPHEN],
        ]);
    }

    /**
     * @param $parts
     * @dataProvider identifierWithCapsProvider
     */
    public function testConvertUppercase($identifier, $format)
    {
        $expected = 'ANIDENTIFIER1ACRNM';

        $result = Converter::convert(
            $identifier,
            $format,
            CaseFormat::UPPERCASE
        );
        $this->assertSame($expected, $result);
    }

    /**
     * @param $parts
     * @dataProvider identifierWithCapsProvider
     */
    public function testConvertLowercase($identifier, $format)
    {
        $expected = 'anidentifier1acrnm';

        $result = Converter::convert(
            $identifier,
            $format,
            CaseFormat::LOWERCASE
        );
        $this->assertSame($expected, $result);
    }

    /**
     * @param $parts
     * @dataProvider identifierWithCapsProvider
     */
    public function testConvertCamelCase($identifier, $format)
    {
        $expected = 'anIdentifier1Acrnm';

        $result = Converter::convert(
            $identifier,
            $format,
            CaseFormat::CAMEL_CASE
        );
        $this->assertSame($expected, $result);
    }

    /**
     * @param $parts
     * @dataProvider identifierWithCapsProvider
     */
    public function testConvertUpperCamelCase($identifier, $format)
    {
        $expected = 'AnIdentifier1Acrnm';

        $result = Converter::convert(
            $identifier,
            $format,
            CaseFormat::UPPER_CAMEL_CASE
        );
        $this->assertSame($expected, $result);
    }

    /**
     * @param $parts
     * @dataProvider identifierProvider
     */
    public function testConvertCamelCaseWithAcronyms($identifier, $format)
    {
        $expected = 'anIdentifier1ACRNM';

        $result = Converter::convert(
            $identifier,
            $format,
            CaseFormat::CAMEL_CASE_WITH_ACRONYMS
        );
        $this->assertSame($expected, $result);
    }

    /**
     * @param $parts
     * @dataProvider identifierProvider
     */
    public function testConvertUpperCamelCaseWithAcronyms($identifier, $format)
    {
        $expected = 'AnIdentifier1ACRNM';

        $result = Converter::convert(
            $identifier,
            $format,
            CaseFormat::UPPER_CAMEL_CASE_WITH_ACRONYMS
        );
        $this->assertSame($expected, $result);
    }

    /**
     * @param $parts
     * @dataProvider identifierWithCapsProvider
     */
    public function testConvertUnderscore($identifier, $format)
    {
        $expected = 'an_identifier1_acrnm';

        $result = Converter::convert(
            $identifier,
            $format,
            CaseFormat::UNDERSCORE
        );
        $this->assertSame($expected, $result);
    }

    /**
     * @param $parts
     * @dataProvider identifierWithCapsProvider
     */
    public function testConvertUpperUnderscore($identifier, $format)
    {
        $expected = 'An_Identifier1_Acrnm';

        $result = Converter::convert(
            $identifier,
            $format,
            CaseFormat::UPPER_UNDERSCORE
        );
        $this->assertSame($expected, $result);
    }

    /**
     * @param $parts
     * @dataProvider identifierWithCapsProvider
     */
    public function testConvertCapsUnderscore($identifier, $format)
    {
        $expected = 'AN_IDENTIFIER1_ACRNM';

        $result = Converter::convert(
            $identifier,
            $format,
            CaseFormat::CAPS_UNDERSCORE
        );
        $this->assertSame($expected, $result);
    }

    /**
     * @param $parts
     * @dataProvider identifierProvider
     */
    public function testConvertUnderscoreWithAcronyms($identifier, $format)
    {
        $expected = 'an_identifier1_ACRNM';

        $result = Converter::convert(
            $identifier,
            $format,
            CaseFormat::UNDERSCORE_WITH_ACRONYMS
        );
        $this->assertSame($expected, $result);
    }

    /**
     * @param $parts
     * @dataProvider identifierProvider
     */
    public function testConvertUpperUnderscoreWithAcronyms($identifier, $format)
    {
        $expected = 'An_Identifier1_ACRNM';

        $result = Converter::convert(
            $identifier,
            $format,
            CaseFormat::UPPER_UNDERSCORE_WITH_ACRONYMS
        );
        $this->assertSame($expected, $result);
    }

    /**
     * @param $parts
     * @dataProvider identifierWithCapsProvider
     */
    public function testConvertHyphen($identifier, $format)
    {
        $expected = 'an-identifier1-acrnm';

        $result = Converter::convert(
            $identifier,
            $format,
            CaseFormat::HYPHEN
        );
        $this->assertSame($expected, $result);
    }

    /**
     * @param $parts
     * @dataProvider identifierWithCapsProvider
     */
    public function testConvertUpperHyphen($identifier, $format)
    {
        $expected = 'An-Identifier1-Acrnm';

        $result = Converter::convert(
            $identifier,
            $format,
            CaseFormat::UPPER_HYPHEN
        );
        $this->assertSame($expected, $result);
    }

    /**
     * @param $parts
     * @dataProvider identifierWithCapsProvider
     */
    public function testConvertCapsHyphen($identifier, $format)
    {
        $expected = 'AN-IDENTIFIER1-ACRNM';

        $result = Converter::convert(
            $identifier,
            $format,
            CaseFormat::CAPS_HYPHEN
        );
        $this->assertSame($expected, $result);
    }

    /**
     * @param $parts
     * @dataProvider identifierProvider
     */
    public function testConvertHyphenWithAcronyms($identifier, $format)
    {
        $expected = 'an-identifier1-ACRNM';

        $result = Converter::convert(
            $identifier,
            $format,
            CaseFormat::HYPHEN_WITH_ACRONYMS
        );
        $this->assertSame($expected, $result);
    }

    /**
     * @param $parts
     * @dataProvider identifierProvider
     */
    public function testConvertUpperHyphenWithAcronyms($identifier, $format)
    {
        $expected = 'An-Identifier1-ACRNM';

        $result = Converter::convert(
            $identifier,
            $format,
            CaseFormat::UPPER_HYPHEN_WITH_ACRONYMS
        );
        $this->assertSame($expected, $result);
    }
}
