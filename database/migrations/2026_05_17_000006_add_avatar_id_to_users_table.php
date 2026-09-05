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
            // Deliberately no onDelete clause — MySQL defaults to RESTRICT,
            // preventing deletion of any avatar that a user still references.
            $table->foreignId('avatar_id')
                ->nullable()
                ->after('avatar_path')
                ->constrained('avatars');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['avatar_id']);
            $table->dropColumn('avatar_id');
        });
    }
};
