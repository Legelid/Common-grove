<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates a user-submitted custom interest tag name.
 *
 * Rules:
 *  1. 2–40 characters (after trimming whitespace)
 *  2. Only Unicode letters, numbers, spaces, hyphens, apostrophes, and ampersands
 *  3. Passes profanity filter (including common leet-speak substitutions)
 */
class ValidCustomTag implements ValidationRule
{
    /** @var array<string, string> */
    private const LEET_MAP = [
        '0' => 'o',
        '1' => 'i',
        '3' => 'e',
        '4' => 'a',
        '5' => 's',
        '7' => 't',
        '@' => 'a',
        '$' => 's',
        '+' => 't',
    ];

    /** @var list<string> */
    private const BLOCKED_TERMS = [
        'nigger', 'nigga', 'faggot', 'fag', 'retard', 'cunt',
        'chink', 'spic', 'kike', 'tranny', 'rape', 'rapist',
        'pedophile', 'pedo', 'nazi', 'hitler',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a string.')->translate();
            return;
        }

        $value  = (string) preg_replace('/\s+/', ' ', trim($value));
        $length = mb_strlen($value);

        if ($length < 2) {
            $fail('Interest tags must be at least 2 characters.')->translate();
            return;
        }

        if ($length > 40) {
            $fail('Interest tags must not exceed 40 characters.')->translate();
            return;
        }

        // Unicode letters, numbers, spaces, hyphens, apostrophes, ampersands
        if (! preg_match("/^[\p{L}\p{N} &'\-]+$/u", $value)) {
            $fail('Interest tags may only contain letters, numbers, spaces, hyphens, apostrophes, and ampersands.')->translate();
            return;
        }

        $normalised = strtr(mb_strtolower($value), self::LEET_MAP);
        foreach (self::BLOCKED_TERMS as $term) {
            if (str_contains($normalised, $term)) {
                $fail('That tag name is not allowed.')->translate();
                return;
            }
        }
    }
}
