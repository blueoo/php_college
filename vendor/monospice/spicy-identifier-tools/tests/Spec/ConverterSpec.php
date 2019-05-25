<?php

namespace Spec\Monospice\SpicyIdentifiers\Tools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Monospice\SpicyIdentifiers\Tools\CaseFormat;

class ConverterSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Monospice\SpicyIdentifiers\Tools\Converter');
    }

    function it_converts_an_identifier_from_one_format_to_another()
    {
        $this::convert(
            'anIdentifierString',
            CaseFormat::CAMEL_CASE,
            CaseFormat::UNDERSCORE
        )->shouldReturn('an_identifier_string');
    }
}
