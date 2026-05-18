<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('profile_status', 120)->nullable()->after('bio');
            $table->string('accent_color', 20)->nullable()->after('profile_status');
            $table->string('banner_style', 40)->nullable()->after('accent_color');
            $table->string('prompt_comfort_thing', 120)->nullable()->after('currently_watching');
            $table->string('prompt_ramble_topic', 120)->nullable()->after('prompt_comfort_thing');
            $table->json('social_styles')->nullable()->after('prompt_ramble_topic');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'profile_status',
                'accent_color',
                'banner_style',
                'prompt_comfort_thing',
                'prompt_ramble_topic',
                'social_styles',
            ]);
        });
    }
};
