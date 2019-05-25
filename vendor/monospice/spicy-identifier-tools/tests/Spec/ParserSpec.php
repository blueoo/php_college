<?php

namespace Spec\Monospice\SpicyIdentifiers\Tools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Monospice\SpicyIdentifiers\Tools\CaseFormat;

class ParserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Monospice\SpicyIdentifiers\Tools\Parser');
    }

    function it_provides_a_method_that_parses_using_a_given_case_format()
    {
        $this::parse('anIdentifierName', CaseFormat::CAMEL_CASE)
            ->shouldReturn(['an', 'Identifier', 'Name']);
    }

    function it_parses_an_identifier_name_in_camel_case()
    {
        $this::parseFromCamelCase('anIdentifierName')
            ->shouldReturn(['an', 'Identifier', 'Name']);
    }

    function it_parses_an_identifier_name_in_camel_case_using_extended_ascii()
    {
        $this::parseFromCamelCaseExtended('änÏdentifierNáme')
            ->shouldReturn(['än', 'Ïdentifier', 'Náme']);
    }

    function it_parses_an_identifier_name_in_underscore_case()
    {
        $this::parseFromUnderscore('an_identifier_name')
            ->shouldReturn(['an', 'identifier', 'name']);
    }

    function it_parses_an_identifier_name_in_hyphenated_case()
    {
        $this::parseFromHyphen('an-identifier-name')
            ->shouldReturn(['an', 'identifier', 'name']);
    }

    function it_parses_an_identifier_with_multiple_cases()
    {
        $this::parseFromMixedCase('an_IdentifierName-string', [
            CaseFormat::UNDERSCORE,
            CaseFormat::CAMEL_CASE,
            CaseFormat::HYPHEN,
        ])->shouldReturn(['an', 'Identifier', 'Name', 'string']);
    }

    function it_tolerates_parsing_mixed_with_only_a_single_format()
    {
        $this::parseFromMixedCase(
            'anIdentifierName',
            [CaseFormat::CAMEL_CASE]
        )
            ->shouldReturn(['an', 'Identifier', 'Name']);
    }

    function it_throws_an_exception_when_calling_parse_with_an_invalid_format()
    {
        $this->shouldThrow('\InvalidArgumentException')
            ->during('parse', ['anIdentifierName', 'InvalidFormat']);
    }
}
