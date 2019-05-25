<?php

namespace Monospice\SpicyIdentifiers\Tools;

use Monospice\SpicyIdentifiers\Tools\Interfaces;
use Monospice\SpicyIdentifiers\Tools\Parser;
use Monospice\SpicyIdentifiers\Tools\Formatter;

/**
 * Converts an identifier string from one format to another
 *
 * @author Cy Rossignol <cy.rossignol@yahoo.com>
 */
class Converter implements Interfaces\Converter
{

    // Inherit Doc from Interfaces\Converter
    public static function convert($identifier, $sourceFormat, $outputFormat)
    {
        $parts = Parser::parse($identifier, $sourceFormat);

        return Formatter::format($parts, $outputFormat);
    }
}
