<?php

namespace Monospice\SpicyIdentifiers\Tools;

/**
 * Constants that represent different identifier case formats
 *
 * @author Cy Rossignol <cy.rossignol@yahoo.com>
 */
class CaseFormat
{

    /**
     * Represents alllowercase (with no delimiter)
     *
     * @var string
     */
    const LOWERCASE = 'Lowercase';

    /**
     * Represents ALLUPPERCASE (with no delimiter)
     *
     * @var string
     */
    const UPPERCASE = 'Uppercase';

    /**
     * Represents camelCase
     *
     * @var string
     */
    const CAMEL_CASE = 'CamelCase';

    /**
     * Represents UpperCamelCase
     *
     * @var string
     */
    const UPPER_CAMEL_CASE = 'UpperCamelCase';

    /**
     * An alias for CaseFormat::UPPER_CAMEL_CASE
     *
     * @var string
     */
    const STUDLY_CAPS = 'UpperCamelCase';

    /**
     * Represents camelCaseWithACRNMS (with Acronyms)
     *
     * @var string
     */
    const CAMEL_CASE_WITH_ACRONYMS = 'CamelCaseWithAcronyms';

    /**
     * Represents UpperCamelCaseWithACRNMS (with Acronyms)
     *
     * @var string
     */
    const UPPER_CAMEL_CASE_WITH_ACRONYMS = 'UpperCamelCaseWithAcronyms';

    /**
     * An alias for CaseFormat::UPPER_CAMEL_CASE_WITH_ACRONYMS
     *
     * @var string
     */
    const STUDLY_CAPS_WITH_ACRONYMS = 'UpperCamelCaseWithAcronyms';

    /**
     * Represents underscore_case
     *
     * @var string
     */
    const UNDERSCORE = 'Underscore';

    /**
     * Represents Upper_Underscore_Case
     *
     * @var string
     */
    const UPPER_UNDERSCORE = 'UpperUnderscore';

    /**
     * Represents CAPITALIZED_UNDERSCORE_CASE
     *
     * @var string
     */
    const CAPS_UNDERSCORE = 'CapsUnderscore';

    /**
     * Represents underscore_case_with_ACRNMS (with Acronyms)
     *
     * @var string
     */
    const UNDERSCORE_WITH_ACRONYMS = 'UnderscoreWithAcronyms';

    /**
     * Represents Upper_Underscore_Case_With_ACRNMS (with Acronyms)
     *
     * @var string
     */
    const UPPER_UNDERSCORE_WITH_ACRONYMS = 'UpperUnderscoreWithAcronyms';

    /**
     * An alias for CaseFormat::UNDERSCORE
     *
     * @var string
     */
    const SNAKE_CASE = 'Underscore';

    /**
     * An alias for CaseFormat::UPPER_UNDERSCORE
     *
     * @var string
     */
    const UPPER_SNAKE_CASE = 'UpperUnderscore';

    /**
     * An alias for CaseFormat::CAPS_UNDERSCORE
     *
     * @var string
     */
    const CAPS_SNAKE_CASE = 'CapsUnderscore';

    /**
     * An alias for CaseFormat::UNDERSCORE_WITH_ACRONYMS
     *
     * @var string
     */
    const SNAKE_CASE_WITH_ACRONYMS = 'UnderscoreWithAcronyms';

    /**
     * An alias for CaseFormat::UPPER_UNDERSCORE_WITH_ACRONYMS
     *
     * @var string
     */
    const UPPER_SNAKE_CASE_WITH_ACRONYMS = 'UpperUnderscoreWithAcronyms';

    /**
     * Represents hyphenated-case
     *
     * @var string
     */
    const HYPHEN = 'Hyphen';

    /**
     * Represents Upper-Hyphenated-Case
     *
     * @var string
     */
    const UPPER_HYPHEN = 'UpperHyphen';

    /**
     * Represents CAPITALIZED-HYPENATED-CASE
     *
     * @var string
     */
    const CAPS_HYPHEN = 'CapsHyphen';

    /**
     * Represents hyphenated-case-with-ACRNMS (with Acronyms)
     *
     * @var string
     */
    const HYPHEN_WITH_ACRONYMS = 'HyphenWithAcronyms';

    /**
     * Represents Upper-Hyphenated-Case-With-ACRNMS (with Acronyms)
     *
     * @var string
     */
    const UPPER_HYPHEN_WITH_ACRONYMS = 'UpperHyphenWithAcronyms';
}
