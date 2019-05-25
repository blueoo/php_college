<?php

namespace Spec\Monospice\SpicyIdentifiers\Tools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Monospice\SpicyIdentifiers\Tools\CaseFormat;

class FormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Monospice\SpicyIdentifiers\Tools\Formatter');
    }

    function it_provides_a_method_that_formats_using_a_given_case_format()
    {
        $this::format(['an', 'identifier'], CaseFormat::CAMEL_CASE)
            ->shouldReturn('anIdentifier');
    }

    function it_formats_a_string_to_uppercase()
    {
        $this::formatUppercase(['an', 'identifier'])
            ->shouldReturn('ANIDENTIFIER');
        $this::format(['an', 'identifier'], CaseFormat::UPPERCASE)
            ->shouldReturn('ANIDENTIFIER');
    }

    function it_formats_a_string_to_lowercase()
    {
        $this::formatLowercase(['an', 'identifier'])
            ->shouldReturn('anidentifier');
        $this::format(['an', 'identifier'], CaseFormat::LOWERCASE)
            ->shouldReturn('anidentifier');
    }

    function it_formats_a_string_to_camel_case()
    {
        $this::formatCamelCase(['an', 'identifier'])
            ->shouldReturn('anIdentifier');
        $this::format(['an', 'identifier'], CaseFormat::CAMEL_CASE)
            ->shouldReturn('anIdentifier');
    }

    function it_formats_a_string_to_upper_camel_case()
    {
        $this::formatUpperCamelCase(['an', 'identifier'])
            ->shouldReturn('AnIdentifier');
        $this::format(['an', 'identifier'], CaseFormat::UPPER_CAMEL_CASE)
            ->shouldReturn('AnIdentifier');
    }

    function it_formats_a_string_to_camel_case_with_acronyms()
    {
        $parts = ['an', 'ACRNM', 'identifier'];

        $this::formatCamelCaseWithAcronyms($parts)
            ->shouldReturn('anACRNMIdentifier');
        $this::format($parts, CaseFormat::CAMEL_CASE_WITH_ACRONYMS)
            ->shouldReturn('anACRNMIdentifier');
    }

    function it_formats_a_string_to_upper_camel_case_with_acronyms()
    {
        $parts = ['an', 'ACRNM', 'identifier'];

        $this::formatUpperCamelCaseWithAcronyms($parts)
            ->shouldReturn('AnACRNMIdentifier');
        $this::format($parts, CaseFormat::UPPER_CAMEL_CASE_WITH_ACRONYMS)
            ->shouldReturn('AnACRNMIdentifier');
    }

    function it_formats_a_string_to_underscore_case()
    {
        $this::formatUnderscore(['an', 'identifier'])
            ->shouldReturn('an_identifier');
        $this::format(['an', 'identifier'], CaseFormat::UNDERSCORE)
            ->shouldReturn('an_identifier');
    }

    function it_formats_a_string_to_upper_underscore_case()
    {
        $this::formatUpperUnderscore(['an', 'identifier'])
            ->shouldReturn('An_Identifier');
        $this::format(['an', 'identifier'], CaseFormat::UPPER_UNDERSCORE)
            ->shouldReturn('An_Identifier');
    }

    function it_formats_a_string_to_capitalized_underscore_case()
    {
        $this::formatCapsUnderscore(['an', 'identifier'])
            ->shouldReturn('AN_IDENTIFIER');
        $this::format(['an', 'identifier'], CaseFormat::CAPS_UNDERSCORE)
            ->shouldReturn('AN_IDENTIFIER');
    }

    function it_formats_a_string_to_underscore_case_with_acronyms()
    {
        $parts = ['an', 'ACRNM', 'identifier'];

        $this::formatUnderscoreWithAcronyms($parts)
            ->shouldReturn('an_ACRNM_identifier');
        $this::format($parts, CaseFormat::UNDERSCORE_WITH_ACRONYMS)
            ->shouldReturn('an_ACRNM_identifier');
    }

    function it_formats_a_string_to_upper_underscore_case_with_acronyms()
    {
        $parts = ['an', 'ACRNM', 'identifier'];

        $this::formatUpperUnderscoreWithAcronyms($parts)
            ->shouldReturn('An_ACRNM_Identifier');
        $this::format($parts, CaseFormat::UPPER_UNDERSCORE_WITH_ACRONYMS)
            ->shouldReturn('An_ACRNM_Identifier');
    }

    function it_formats_a_string_to_hyphenated_case()
    {
        $this::formatHyphen(['an', 'identifier'])
            ->shouldReturn('an-identifier');
        $this::format(['an', 'identifier'], CaseFormat::HYPHEN)
            ->shouldReturn('an-identifier');
    }

    function it_formats_a_string_to_upper_hyphenated_case()
    {
        $this::formatUpperHyphen(['an', 'identifier'])
            ->shouldReturn('An-Identifier');
        $this::format(['an', 'identifier'], CaseFormat::UPPER_HYPHEN)
            ->shouldReturn('An-Identifier');
    }

    function it_formats_a_string_to_capitalized_hyphenated_case()
    {
        $this::formatCapsHyphen(['an', 'identifier'])
            ->shouldReturn('AN-IDENTIFIER');
        $this::format(['an', 'identifier'], CaseFormat::CAPS_HYPHEN)
            ->shouldReturn('AN-IDENTIFIER');
    }

    function it_formats_a_string_to_hyphenated_case_with_acronyms()
    {
        $parts = ['an', 'ACRNM', 'identifier'];

        $this::formatHyphenWithAcronyms($parts)
            ->shouldReturn('an-ACRNM-identifier');
        $this::format($parts, CaseFormat::HYPHEN_WITH_ACRONYMS)
            ->shouldReturn('an-ACRNM-identifier');
    }

    function it_formats_a_string_to_upper_hyphenated_case_with_acronyms()
    {
        $parts = ['an', 'ACRNM', 'identifier'];

        $this::formatUpperHyphenWithAcronyms($parts)
            ->shouldReturn('An-ACRNM-Identifier');
        $this::format($parts, CaseFormat::UPPER_HYPHEN_WITH_ACRONYMS)
            ->shouldReturn('An-ACRNM-Identifier');
    }
}
