<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates a gamertag against the CommonGround ruleset.
 *
 * Rules:
 *  1. 3–20 characters
 *  2. Starts with a letter
 *  3. Only letters, numbers, underscores, hyphens: ^[a-zA-Z][a-zA-Z0-9_-]+$
 *  4. Must contain at least one vowel
 *  5. No 6 or more consecutive consonants
 *  6. No 3 or more consecutive identical characters
 *  7. Passes profanity filter (including common leet-speak substitutions)
 */
class ValidGamertag implements ValidationRule
{
    /**
     * Common leet-speak substitution map for profanity normalisation.
     */
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

    /**
     * Blocked terms (normalised, lower-case). Extend as needed.
     *
     * @var list<string>
     */
    private const BLOCKED_TERMS = [
        'nigger', 'nigga', 'faggot', 'fag', 'retard', 'cunt',
        'chink', 'spic', 'kike', 'tranny', 'rape', 'rapist',
        'pedophile', 'pedo', 'nazi', 'hitler',
    ];

    /**
     * Run the validation rule.
     *
     * @param  string   $attribute
     * @param  mixed    $value
     * @param  Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a string.')->translate();
            return;
        }

        // Rule 1: length 3–20
        $length = strlen($value);
        if ($length < 3 || $length > 20) {
            $fail('The :attribute must be between 3 and 20 characters.')->translate();
            return;
        }

        // Rule 2 & 3: starts with a letter, valid characters only
        if (! preg_match('/^[a-zA-Z][a-zA-Z0-9_-]+$/', $value)) {
            $fail('The :attribute must start with a letter and contain only letters, numbers, underscores, and hyphens.')->translate();
            return;
        }

        // Rule 4: must contain at least one vowel
        if (! preg_match('/[aeiouAEIOU]/', $value)) {
            $fail('The :attribute must contain at least one vowel.')->translate();
            return;
        }

        // Rule 5: no 6+ consecutive consonants
        // Treat digits, underscores, hyphens as non-vowels but not consonants —
        // strip them out before the consonant run check so they don't inflate counts.
        $lettersOnly = (string) preg_replace('/[^a-zA-Z]/', '', $value);
        if (preg_match('/[^aeiouAEIOU]{6,}/i', $lettersOnly)) {
            $fail('The :attribute cannot contain 6 or more consecutive consonants.')->translate();
            return;
        }

        // Rule 6: no 3+ consecutive identical characters
        if (preg_match('/(.)\1{2}/u', $value)) {
            $fail('The :attribute cannot contain 3 or more consecutive identical characters.')->translate();
            return;
        }

        // Rule 7: profanity filter with leet-speak normalisation
        $normalised = $this->normaliseLeet(strtolower($value));
        foreach (self::BLOCKED_TERMS as $term) {
            if (str_contains($normalised, $term)) {
                $fail('The :attribute contains a term that is not allowed.')->translate();
                return;
            }
        }
    }

    /**
     * Normalise leet-speak substitutions in a string for profanity checking.
     *
     * @param  string  $value  Lower-cased input string.
     * @return string          Normalised string.
     */
    private function normaliseLeet(string $value): string
    {
        return strtr($value, self::LEET_MAP);
    }
}
