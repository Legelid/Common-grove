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
            $table->dropColumn(['prompt_comfort_thing', 'prompt_ramble_topic']);
            $table->json('comfort_things')->nullable()->after('currently_watching');
            $table->json('open_to')->nullable()->after('comfort_things');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['comfort_things', 'open_to']);
            $table->string('prompt_comfort_thing', 120)->nullable()->after('currently_watching');
            $table->string('prompt_ramble_topic', 120)->nullable()->after('prompt_comfort_thing');
        });
    }
};
