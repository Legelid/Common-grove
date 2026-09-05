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
            $table->string('bio', 300)->nullable()->after('display_name');
            $table->string('avatar_path')->nullable()->after('bio');
            $table->timestamp('last_seen_at')->nullable()->after('avatar_path');
            $table->timestamp('last_gamertag_changed_at')->nullable()->after('last_seen_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'bio',
                'avatar_path',
                'last_seen_at',
                'last_gamertag_changed_at',
            ]);
        });
    }
};
