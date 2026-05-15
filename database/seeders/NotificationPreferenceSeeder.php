<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationPreferenceSeeder extends Seeder
{
    public function run(): void
    {
        User::withTrashed()->chunk(100, function ($users): void {
            foreach ($users as $user) {
                NotificationPreference::firstOrCreate(['user_id' => $user->id]);
            }
        });

        $this->command->info('Notification preferences seeded for existing users.');
    }
}
