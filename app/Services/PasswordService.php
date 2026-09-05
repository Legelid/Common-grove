<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Hash;

/**
 * Handles password hashing and verification with Argon2id + pepper.
 *
 * The pepper is a server-side secret appended to all passwords before hashing.
 * A compromised database alone is insufficient to crack passwords — the attacker
 * would also need the pepper value from the application config.
 */
class PasswordService
{
    private string $pepper;

    /**
     * Create a new PasswordService instance.
     *
     * @throws \RuntimeException If PASSWORD_PEPPER is not configured.
     */
    public function __construct()
    {
        $pepper = (string) config('app.password_pepper');

        if (empty($pepper)) {
            throw new \RuntimeException('PASSWORD_PEPPER is not configured in .env');
        }

        $this->pepper = $pepper;
    }

    /**
     * Hash a plain-text password using Argon2id with pepper.
     *
     * @param  string  $password  The plain-text password to hash.
     * @return string             The resulting hash string.
     */
    public function hash(string $password): string
    {
        return Hash::make($password . $this->pepper);
    }

    /**
     * Verify a plain-text password against a stored hash.
     *
     * Uses Hash::check() to prevent timing attacks — never use string equality.
     *
     * @param  string  $password  The plain-text password to verify.
     * @param  string  $hash      The stored Argon2id hash.
     * @return bool               True if the password matches the hash.
     */
    public function verify(string $password, string $hash): bool
    {
        return Hash::check($password . $this->pepper, $hash);
    }

    /**
     * Determine whether the given hash needs to be rehashed.
     *
     * Returns true when the hashing work factor has changed, indicating the
     * hash should be regenerated at the next successful login.
     *
     * @param  string  $hash  The stored hash to inspect.
     * @return bool           True if the hash needs to be rehashed.
     */
    public function needsRehash(string $hash): bool
    {
        return Hash::needsRehash($hash);
    }
}
