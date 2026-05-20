<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Optional demo seeder — development and staging only.
 * Creates fake users, hangout posts, and persistent demo rooms.
 * NEVER run on a production database.
 *
 * Usage: php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CommonGroveDemoSeeder::class,
            CommonGroveDemoSeeder::class,
        ]);
    }
}
