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
            $table->boolean('is_first_roots')->default(false)->after('is_supporter');
            $table->timestamp('first_roots_awarded_at')->nullable()->after('is_first_roots');
            $table->uuid('beta_invite_id')->nullable()->after('first_roots_awarded_at');

            $table->index('beta_invite_id');
            $table->foreign('beta_invite_id')->references('id')->on('beta_invites')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['beta_invite_id']);
            $table->dropColumn(['is_first_roots', 'first_roots_awarded_at', 'beta_invite_id']);
        });
    }
};
