<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tags', function (Blueprint $table): void {
            // 'curated' = seeded/admin-approved  |  'custom' = user-created pending review
            $table->string('source', 10)->default('curated')->after('slug');

            $table->uuid('created_by_user_id')->nullable()->after('source');
            $table->foreign('created_by_user_id')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable()->after('is_approved');

            $table->index('source');
            $table->index('created_by_user_id');
        });

        // Stamp pre-existing user-submitted tags (uncurated + unapproved) as custom.
        DB::statement("UPDATE tags SET source = 'custom' WHERE is_curated = 0 AND is_approved = 0");
    }

    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table): void {
            $table->dropForeign(['created_by_user_id']);
            $table->dropIndex(['source']);
            $table->dropIndex(['created_by_user_id']);
            $table->dropColumn(['source', 'created_by_user_id', 'approved_at']);
        });
    }
};
