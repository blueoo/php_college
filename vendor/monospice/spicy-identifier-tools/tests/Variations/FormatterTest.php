<?php

namespace Monospice\SpicyIdentifiers\Tools\Tests;

use PHPUnit_Framework_TestCase;

use Monospice\SpicyIdentifiers\Tools\Formatter;

class FormatterTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    /**
     * Returns a set of test data containing identifer parts without all caps
     * for use with tests that preserve acronyms
     *
     * @return array The array of identifier parts
     */
    public function identifierPartsProvider()
    {
        return [
            [['an', 'identifier1', 'ACRNM']],
            [['an', 'Identifier1', 'ACRNM']],
            [['An', 'Identifier1', 'ACRNM']],
            [['An', 'identifier1', 'ACRNM']],
        ];
    }

    /**
     * Returns a set of test data containing identifer parts with all caps
     * for use with tests that don't preserve acronyms
     *
     * @return array The array of identifier parts
     */
    public function identifierPartsWithCapsProvider()
    {
        return array_merge($this->identifierPartsProvider(), [
            [['AN', 'IDENTIFIER1', 'ACRNM']],
        ]);
    }

    /**
     * @param $parts
     * @dataProvider identifierPartsWithCapsProvider
     */
    public function testFormatUppercase($parts)
    {
        $this->assertSame(
            'ANIDENTIFIER1ACRNM',
            Formatter::formatUppercase($parts)
        );
    }

    /**
     * @param $parts
     * @dataProvider identifierPartsWithCapsProvider
     */
    public function testFormatLowercase($parts)
    {
        $this->assertSame(
            'anidentifier1acrnm',
            Formatter::formatLowercase($parts)
        );
    }

    /**
     * @param $parts
     * @dataProvider identifierPartsWithCapsProvider
     */
    public function testFormatCamelCase($parts)
    {
        $this->assertSame(
            'anIdentifier1Acrnm',
            Formatter::formatCamelCase($parts)
        );
    }

    /**
     * @param $parts
     * @dataProvider identifierPartsWithCapsProvider
     */
    public function testFormatUpperCamelCase($parts)
    {
        $this->assertSame(
            'AnIdentifier1Acrnm',
            Formatter::formatUpperCamelCase($parts)
        );
    }

    /**
     * @param $parts
     * @dataProvider identifierPartsProvider
     */
    public function testFormatCamelCaseWithAcronyms($parts)
    {
        $this->assertSame(
            'anIdentifier1ACRNM',
            Formatter::formatCamelCaseWithAcronyms($parts)
        );
    }

    /**
     * @param $parts
     * @dataProvider identifierPartsProvider
     */
    public function testFormatUpperCamelCaseWithAcronyms($parts)
    {
        $this->assertSame(
            'AnIdentifier1ACRNM',
            Formatter::formatUpperCamelCaseWithAcronyms($parts)
        );
    }

    /**
     * @param $parts
     * @dataProvider identifierPartsWithCapsProvider
     */
    public function testFormatUnderscore($parts)
    {
        $this->assertSame(
            'an_identifier1_acrnm',
            Formatter::formatUnderscore($parts)
        );
    }

    /**
     * @param $parts
     * @dataProvider identifierPartsWithCapsProvider
     */
    public function testFormatUpperUnderscore($parts)
    {
        $this->assertSame(
            'An_Identifier1_Acrnm',
            Formatter::formatUpperUnderscore($parts)
        );
    }

    /**
     * @param $parts
     * @dataProvider identifierPartsWithCapsProvider
     */
    public function testFormatCapsUnderscore($parts)
    {
        $this->assertSame(
            'AN_IDENTIFIER1_ACRNM',
            Formatter::formatCapsUnderscore($parts)
        );
    }

    /**
     * @param $parts
     * @dataProvider identifierPartsProvider
     */
    public function testFormatUnderscoreWithAcronyms($parts)
    {
        $this->assertSame(
            'an_identifier1_ACRNM',
            Formatter::formatUnderscoreWithAcronyms($parts)
        );
    }

    /**
     * @param $parts
     * @dataProvider identifierPartsProvider
     */
    public function testFormatUpperUnderscoreWithAcronyms($parts)
    {
        $this->assertSame(
            'An_Identifier1_ACRNM',
            Formatter::formatUpperUnderscoreWithAcronyms($parts)
        );
    }

    /**
     * @param $parts
     * @dataProvider identifierPartsWithCapsProvider
     */
    public function testFormatHyphen($parts)
    {
        $this->assertSame(
            'an-identifier1-acrnm',
            Formatter::formatHyphen($parts)
        );
    }

    /**
     * @param $parts
     * @dataProvider identifierPartsWithCapsProvider
     */
    public function testFormatUpperHyphen($parts)
    {
        $this->assertSame(
            'An-Identifier1-Acrnm',
            Formatter::formatUpperHyphen($parts)
        );
    }

    /**
     * @param $parts
     * @dataProvider identifierPartsWithCapsProvider
     */
    public function testFormatCapsHyphen($parts)
    {
        $this->assertSame(
            'AN-IDENTIFIER1-ACRNM',
            Formatter::formatCapsHyphen($parts)
        );
    }

    /**
     * @param $parts
     * @dataProvider identifierPartsProvider
     */
    public function testFormatHyphenWithAcronyms($parts)
    {
        $this->assertSame(
            'an-identifier1-ACRNM',
            Formatter::formatHyphenWithAcronyms($parts)
        );
    }

    /**
     * @param $parts
     * @dataProvider identifierPartsProvider
     */
    public function testFormatUpperHyphenWithAcronyms($parts)
    {
        $this->assertSame(
            'An-Identifier1-ACRNM',
            Formatter::formatUpperHyphenWithAcronyms($parts)
        );
    }
}
