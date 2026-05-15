<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'gamertag'            => fake()->unique()->regexify('[A-Z][a-z]{2,5}[aeiou][a-z]{0,3}[0-9]{0,2}'),
            'display_name'        => null,
            'email'               => fake()->unique()->safeEmail(),
            'email_verified_at'   => now(),
            'password'            => static::$password ??= Hash::make('password'),
            'identity_mode'       => 1,
            'show_names_pref'     => true,
            'onboarding_completed' => false,
            'remember_token'      => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function onboarded(): static
    {
        return $this->state(fn (array $attributes) => [
            'onboarding_completed' => true,
        ]);
    }
}
