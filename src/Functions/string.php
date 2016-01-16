<?php

namespace Undine\Functions;

function escape($text)
{
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Formats a string for HTML display by replacing variable placeholders.
 *
 * This function replaces variable placeholders in a string with the requested values and escapes
 * the values so they can be safely displayed as HTML. It should be used on any unknown text that
 * is intended to be printed to an HTML page (especially text that may have come from untrusted
 * users, since in that case it prevents cross-site scripting and other security problems).
 *
 * @param string   $string A string containing placeholders.
 * @param string[] $args   An associative array of replacements to make. Occurrences in $string of
 *                         any key in $args are replaced with the corresponding value, after optional
 *                         sanitization and formatting. The type of sanitization and formatting
 *                         depends on the first character of the key:
 *                         - @variable: Escaped to HTML.
 *                         - *variable: Escaped to HTML and formatted as <strong>emphasized</strong> text.
 *                         - %variable: Escaped to HTML and formatted as <em>emphasized</em> text.
 *                         - !variable: Inserted as is, with no sanitization or formatting.
 *
 * @return string
 */
function format($string, array $args)
{
    // Transform arguments before inserting them.
    foreach ($args as $key => $value) {
        switch ($key[0]) {
            case '@':
                // Escaped only.
                $args[$key] = escape($value);
                break;
            case '%':
                // Escaped and emphasised.
                $value = escape($value);
                $args[$key] = '<em>'.$value.'</em>';
                break;
            case '*':
                // Escaped and strong.
                $value = escape($value);
                $args[$key] = '<strong>'.$value.'</strong>';
                break;
            case '!':
                // Pass-through.
                break;
            default:
                throw new \RuntimeException('String format type not provided.');
        }
    }

    return strtr($string, $args);
}
