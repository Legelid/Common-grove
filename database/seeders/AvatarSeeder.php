<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Avatar;
use App\Models\User;
use Illuminate\Database\Seeder;

class AvatarSeeder extends Seeder
{
    /**
     * Seed the avatars table from config/avatars.php and migrate any existing
     * users who still have a legacy `curated:{category}/{slug}` avatar_path
     * to the new avatar_id system.
     */
    public function run(): void
    {
        $this->seedFromConfig();
        $this->migrateUserPaths();
    }

    private function seedFromConfig(): void
    {
        /** @var array<string, array{label: string, avatars: array<string, string>}> $catalog */
        $catalog = config('avatars.categories');

        foreach ($catalog as $category => $data) {
            foreach ($data['avatars'] as $slug => $name) {
                Avatar::firstOrCreate(
                    ['category' => $category, 'slug' => $slug],
                    [
                        'name'       => $name,
                        'image_path' => "avatars/curated/{$category}/{$slug}.svg",
                        'status'     => 'active',
                    ]
                );
            }
        }
    }

    /**
     * Convert legacy `curated:{category}/{slug}` paths on the users table to
     * avatar_id references. Idempotent: skips users who already have avatar_id.
     */
    private function migrateUserPaths(): void
    {
        User::whereNotNull('avatar_path')
            ->where('avatar_path', 'like', 'curated:%')
            ->whereNull('avatar_id')
            ->each(function (User $user): void {
                $key = substr((string) $user->avatar_path, 8); // strip "curated:"
                [$category, $slug] = array_pad(explode('/', $key, 2), 2, '');

                $avatar = Avatar::where('category', $category)
                    ->where('slug', $slug)
                    ->first();

                if ($avatar) {
                    $user->updateQuietly([
                        'avatar_id'   => $avatar->id,
                        'avatar_path' => null,
                    ]);
                }
            });
    }
}
