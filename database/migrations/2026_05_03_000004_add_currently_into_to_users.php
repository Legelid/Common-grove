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
            $table->string('currently_playing', 60)->nullable()->after('status_expires_at');
            $table->string('currently_reading', 60)->nullable()->after('currently_playing');
            $table->string('currently_watching', 60)->nullable()->after('currently_reading');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['currently_playing', 'currently_reading', 'currently_watching']);
        });
    }
};
