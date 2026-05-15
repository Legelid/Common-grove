<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Rules\ValidGamertag;
use Illuminate\Support\Facades\Validator;

/**
 * Generates available gamertag suggestions when a chosen gamertag is taken.
 *
 * Strategy (in order, stop once 4–6 valid suggestions are collected):
 *  1. Number variants — append a random 2–4 digit number or the current year
 *  2. Prefix/suffix word list — prepend or append a word from the curated list
 *  3. Single character substitution — one leet-speak swap (a→4, i→1, o→0, e→3)
 *
 * All suggestions pass ValidGamertag rules and are confirmed available in the DB
 * before being returned.
 */
class GamertagSuggestionService
{
    private const WORD_LIST = [
        'Dark', 'Night', 'Neo', 'Echo', 'Ghost', 'Neon', 'Void', 'Storm',
        'Blaze', 'Pixel', 'X', 'GG', 'Pro', 'Real', 'IRL',
    ];

    private const LEET_SWAPS = [
        'a' => '4',
        'i' => '1',
        'o' => '0',
        'e' => '3',
    ];

    private const TARGET_COUNT = 5;
    private const MAX_ATTEMPTS = 40;

    /**
     * Generate 4–6 available gamertag suggestions for a taken gamertag.
     *
     * @param  string  $taken  The gamertag that was already taken.
     * @return list<string>    Available suggestions, each passing ValidGamertag.
     */
    public function suggest(string $taken): array
    {
        $suggestions = [];
        $seen        = [];

        $candidates = array_merge(
            $this->numberVariants($taken),
            $this->wordVariants($taken),
            $this->leetVariants($taken),
        );

        // Shuffle so repeated calls return different orderings
        shuffle($candidates);

        foreach ($candidates as $candidate) {
            if (count($suggestions) >= self::TARGET_COUNT) {
                break;
            }

            if (isset($seen[$candidate])) {
                continue;
            }

            $seen[$candidate] = true;

            if ($this->isValid($candidate) && $this->isAvailable($candidate)) {
                $suggestions[] = $candidate;
            }
        }

        return $suggestions;
    }

    /**
     * Check whether a gamertag is already taken (case-insensitive).
     *
     * @param  string  $gamertag
     * @return bool
     */
    public function isTaken(string $gamertag): bool
    {
        return User::withTrashed()
            ->whereRaw('LOWER(gamertag) = ?', [strtolower($gamertag)])
            ->exists();
    }

    /**
     * Generate number-appended variants.
     *
     * @param  string  $base
     * @return list<string>
     */
    private function numberVariants(string $base): array
    {
        $variants = [];
        $year     = (int) date('Y');

        // Trim base to leave room for digits (max gamertag length is 20)
        $trimmed = substr($base, 0, 16);

        $variants[] = $trimmed . $year;
        $variants[] = $trimmed . ($year % 100);

        for ($i = 0; $i < 8; $i++) {
            $digits = (string) random_int(10, 9999);
            $safe   = substr($base, 0, 20 - strlen($digits));
            $variants[] = $safe . $digits;
        }

        return $variants;
    }

    /**
     * Generate prefix/suffix word variants.
     *
     * @param  string  $base
     * @return list<string>
     */
    private function wordVariants(string $base): array
    {
        $variants = [];

        foreach (self::WORD_LIST as $word) {
            $suffixed = substr($base, 0, 20 - strlen($word)) . $word;
            $prefixed = substr($word . $base, 0, 20);

            $variants[] = $suffixed;
            $variants[] = $prefixed;
        }

        return $variants;
    }

    /**
     * Generate single-character leet-speak substitution variants (max 1 swap).
     *
     * @param  string  $base
     * @return list<string>
     */
    private function leetVariants(string $base): array
    {
        $variants = [];
        $lower    = strtolower($base);

        foreach (self::LEET_SWAPS as $letter => $digit) {
            $pos = strpos($lower, $letter);
            if ($pos === false) {
                continue;
            }

            // Replace only the first occurrence
            $variant = substr($base, 0, $pos) . $digit . substr($base, $pos + 1);
            $variants[] = $variant;
        }

        return $variants;
    }

    /**
     * Check whether a candidate passes the ValidGamertag rule.
     *
     * @param  string  $candidate
     * @return bool
     */
    private function isValid(string $candidate): bool
    {
        $validator = Validator::make(
            ['gamertag' => $candidate],
            ['gamertag' => [new ValidGamertag()]],
        );

        return ! $validator->fails();
    }

    /**
     * Check whether a candidate is available in the database.
     *
     * @param  string  $candidate
     * @return bool
     */
    private function isAvailable(string $candidate): bool
    {
        return ! $this->isTaken($candidate);
    }
}
