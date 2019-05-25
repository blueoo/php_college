<?php

namespace Monospice\SpicyIdentifiers\Tools\Interfaces;

/**
 * Converts an identifier string from one format to another
 *
 * @author Cy Rossignol <cy.rossignol@yahoo.com>
 */
interface Converter
{

    /**
     * Converts an identifier from one format to another
     *
     * @param string $identifier The identifier string to Converts
     * @param string $sourceFormat The string constant representing the source
     * format
     * @param string $outputFormat The string constant representing the output
     * format
     *
     * @return string The converted identifier string
     */
    public static function convert($identifier, $sourceFormat, $outputFormat);
}
