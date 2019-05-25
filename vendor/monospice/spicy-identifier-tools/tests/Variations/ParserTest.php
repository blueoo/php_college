<?php

namespace Monospice\SpicyIdentifiers\Tools\Tests;

use PHPUnit_Framework_TestCase;

use Monospice\SpicyIdentifiers\Tools\Parser;
use Monospice\SpicyIdentifiers\Tools\CaseFormat;

class ParserTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    /**
     * @param string $identifier
     * @param array $parsed
     * @dataProvider camelCaseIdentiferProvider
     */
    public function testParseFromCamelCase($identifier, array $parsed)
    {
        $this->assertSame(Parser::parseFromCamelCase($identifier), $parsed);
    }

    public function camelCaseIdentiferProvider()
    {
        return [
            ['anIdentifier1String', ['an', 'Identifier1', 'String']],
            ['AnIdentifier1String', ['An', 'Identifier1', 'String']],
            ['anIdentifier1ACRNM', ['an', 'Identifier1', 'ACRNM']],
            ['anACRNM1Identifier', ['an', 'ACRNM1', 'Identifier' ]],
            ['anACRNM1IDString', ['an', 'ACRNM1', 'ID', 'String']],
            ['anACRNM1ID1String', ['an', 'ACRNM1', 'ID1', 'String']],
            ['_anIdentifier1String', ['_an', 'Identifier1', 'String']],
            ['anIdentifier1_String', ['an', 'Identifier1_', 'String']],
            ['anIdentifier1_string', ['an', 'Identifier1_string']],
            ['anIdentifier1Str_ing', ['an', 'Identifier1', 'Str_ing']],
        ];
    }

    /**
     * @param string $identifier
     * @param array $parsed
     * @dataProvider camelCaseExtendedIdentifierProvider
     */
    public function testParseFromCamelCaseExtended($identifier, array $parsed)
    {
        $this->assertSame(Parser::parseFromCamelCaseExtended($identifier), $parsed);
    }

    public function camelCaseExtendedIdentifierProvider()
    {
        return [
            ['änÏdentifier1Náme', ['än', 'Ïdentifier1', 'Náme']],
            ['ÄnÏdentifier1Náme', ['Än', 'Ïdentifier1', 'Náme']],
            ['änÏdentifier1ÄCRNM', ['än', 'Ïdentifier1', 'ÄCRNM']],
            ['änÄCRNM1Ïdentifier', ['än', 'ÄCRNM1', 'Ïdentifier']],
            ['änÄCRNM1ÏDStriñg', ['än', 'ÄCRNM1', 'ÏD', 'Striñg']],
            ['änÄCRNM1ÏD1Striñg', ['än', 'ÄCRNM1', 'ÏD1', 'Striñg']],
            ['_änÏdentifier1Náme', ['_än', 'Ïdentifier1', 'Náme']],
            ['_änÏdentifier1_Náme', ['_än', 'Ïdentifier1_', 'Náme']],
            ['_änÏdentifier1_náme', ['_än', 'Ïdentifier1_náme']],
            ['änÏdentifier1Ná_me', ['än', 'Ïdentifier1', 'Ná_me']],
        ];
    }

    /**
     * @param string $identifier
     * @param array $parsed
     * @dataProvider underscoreIdentiferProvider
     */
    public function testParseFromUnderscore($identifier, array $parsed)
    {
        $this->assertSame(Parser::parseFromUnderscore($identifier), $parsed);
    }

    public function underscoreIdentiferProvider()
    {
        return [
            ['an_identifier1_string', ['an', 'identifier1', 'string']],
            ['An_Identifier1_String', ['An', 'Identifier1', 'String']],
            ['an_Identifier1_String', ['an', 'Identifier1', 'String']],
            ['an_identifier1_ACRNM', ['an', 'identifier1', 'ACRNM']],
            ['_an_identifier1_string', ['', 'an', 'identifier1', 'string']],
            ['an_identifier1_string_', ['an', 'identifier1', 'string', '']],
        ];
    }

    /**
     * @param string $identifier
     * @param array $parsed
     * @dataProvider hyphenIdentiferProvider
     */
    public function testParseFromHyphen($identifier, array $parsed)
    {
        $this->assertSame(Parser::parseFromHyphen($identifier), $parsed);
    }

    public function hyphenIdentiferProvider()
    {
        return [
            ['an-identifier1-string', ['an', 'identifier1', 'string']],
            ['An-Identifier1-String', ['An', 'Identifier1', 'String']],
            ['an-Identifier1-String', ['an', 'Identifier1', 'String']],
            ['an-identifier1-ACRNM', ['an', 'identifier1', 'ACRNM']],
            ['-an-identifier1-string', ['', 'an', 'identifier1', 'string']],
            ['an-identifier1-string-', ['an', 'identifier1', 'string', '']],
        ];
    }

    /**
     * @param string $identifier
     * @param array $parsed
     * @dataProvider mixedCaseIdentiferProvider
     */
    public function testParseFromMixedCase(
        $identifier,
        array $parsed,
        array $formats
    ) {
        $this->assertSame(
            Parser::parseFromMixedCase($identifier, $formats),
            $parsed
        );
    }

    public function mixedCaseIdentiferProvider()
    {
        return [
            [
                'anIdentifier1String',
                ['an', 'Identifier1', 'String'],
                [CaseFormat::CAMEL_CASE],
            ],
            [
                'anIdentifier1Str_ing',
                ['an', 'Identifier1', 'Str', 'ing'],
                [CaseFormat::UNDERSCORE, CaseFormat::CAMEL_CASE],
            ],
            // reverse the formats order from the above
            [
                'anIdentifier1Str_ing',
                ['an', 'Identifier1', 'Str', 'ing'],
                [CaseFormat::CAMEL_CASE, CaseFormat::UNDERSCORE],
            ],
            [
                'an_Identifier1String',
                ['an', 'Identifier1', 'String'],
                [CaseFormat::CAMEL_CASE, CaseFormat::UNDERSCORE],
            ],
            [
                'an_Identifier1-String',
                ['an', 'Identifier1', 'String'],
                [
                    CaseFormat::CAMEL_CASE,
                    CaseFormat::UNDERSCORE,
                    CaseFormat::HYPHEN
                ],
            ],
            [
                'an_-Identifier1String',
                ['an', '', 'Identifier1', 'String'],
                [
                    CaseFormat::CAMEL_CASE,
                    CaseFormat::UNDERSCORE,
                    CaseFormat::HYPHEN
                ],
            ],
        ]; // of return array
    } // of function mixedCaseIdentiferProvider()

    /**
     * @param string $identifier
     * @param array $parsed
     * @dataProvider commonIdentifierProvider
     */
    public function testCommonIdentifiers($identifier, array $parsed)
    {
        $this->assertSame(Parser::parseFromCamelCase($identifier), $parsed);
    }

    public function commonIdentifierProvider()
    {
        return [
            ['XMLHttpRequest', ['XML', 'Http', 'Request']],
        ];
    }
}
