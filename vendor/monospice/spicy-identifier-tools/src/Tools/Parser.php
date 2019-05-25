<?php

namespace Monospice\SpicyIdentifiers\Tools;

use Monospice\SpicyIdentifiers\Tools\CaseFormat;
use Monospice\SpicyIdentifiers\Tools\Interfaces;

/**
 * Parses an identifer string into an array of parts
 *
 * @author Cy Rossignol <cy.rossignol@yahoo.com>
 */
class Parser implements Interfaces\Parser
{
    // Inherit Doc from Interfaces\Parser
    public static function parse($identifier, $format)
    {
        $parseMethod = 'parseFrom' . $format;

        if (method_exists(get_class(), $parseMethod)) {
            return static::$parseMethod($identifier);
        }

        throw new \InvalidArgumentException($format . ' is an unsupported ' .
            'parse format. If applicable, "UPPERCASE_*" and "*_WITH_ACRONYM" ' .
            'formats should be parsed as the corresponding generic format');
    }

    /**
     * Parses each part of an identifier parts array using the given format
     * and merges the new parts into the array
     *
     * @param array $parts The array of identifier parts to parse
     * @param string $format The string constant representing the format to
     * parse from
     *
     * @return array The resulting array of parsed identifier parts
     */
    protected static function parseEachPart(array $parts, $format)
    {
        $lastPartKey = count($parts) - 1;

        for ($p = 0; $p <= $lastPartKey; $p++) {
            $parsedPart = static::parse($parts[$p], $format);
            $numNewParts = count($parsedPart);

            if ($numNewParts > 1) {
                array_splice($parts, $p, 1, $parsedPart);
                $p += $numNewParts;
                $lastPartKey += $numNewParts - 1;
            }
        } // end of parts for loop

        return $parts;
    }

    // Inherit Doc from Interfaces\Parser
    public static function parseFromMixedCase($identifier, array $formats)
    {
        $partsArray = [$identifier];
        $parseCamelCaseLast = false;

        foreach ($formats as $format) {
            if ($format === CaseFormat::CAMEL_CASE) {
                $parseCamelCaseLast = true;
                continue;
            }

            $partsArray = static::parseEachPart($partsArray, $format);
        }

        if ($parseCamelCaseLast) {
            $partsArray = static::parseEachPart(
                $partsArray,
                CaseFormat::CAMEL_CASE
            );
        }

        return $partsArray;
    }

    // Inherit Doc from Interfaces\Parser
    public static function parseFromCamelCase($identifier)
    {
        $camelCasePattern = '/' .
            // Do not attempt to split along a capital letter at the
            // beginning of the string:
            '(?!^)' .
            // Split along any of the following:
            '(' .
                // A sequence that starts with a capital letter following a
                // lowercase letter or number:
                '(?<=[a-z0-9])(?=[A-Z])' .
                // Or
                '|' .
                // A sequence that starts with a capital letter followed by a
                // lowercase letter or number:
                '(?=[A-Z][a-z])' .
            ')' .
        '/';

        return preg_split($camelCasePattern, $identifier);
    }

    // Inherit Doc from Interfaces\Parser
    public static function parseFromCamelCaseExtended(
        $identifier,
        $upper = '\xc0-\xd6\xd8-\xdf',
        $lower = '\x7f-\xbf\xd7\xe0-\xff'
    ) {

        $camelCasePattern = '/' .
            // Do not attempt to split along a capital letter at the
            // beginning of the string:
            '(?!^)' .
            // Split along any of the following:
            '(' .
                // A sequence that starts with a capital letter following a
                // lowercase letter or number:
                '(?<=[a-z0-9' . $lower . '])(?=[A-Z' . $upper . '])' .
                // Or
                '|' .
                // A sequence that starts with a capital letter followed by a
                // lowercase letter or number:
                '(?=[A-Z' . $upper . '][a-z' . $lower . '])' .
            ')' .
        // Accept multibyte characters in the identifier
        '/u';

        return preg_split($camelCasePattern, $identifier);
    }

    // Inherit Doc from Interfaces\Parser
    public static function parseFromUnderscore($identifier)
    {
        return explode('_', $identifier);
    }

    // Inherit Doc from Interfaces\Parser
    public static function parseFromHyphen($identifier)
    {
        return explode('-', $identifier);
    }
}
