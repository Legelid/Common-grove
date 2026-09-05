<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table): void {
            $table->boolean('is_pinned')->default(false)->after('read_at');
            $table->timestamp('pinned_at')->nullable()->after('is_pinned');
            $table->uuid('pinned_by')->nullable()->after('pinned_at');

            $table->foreign('pinned_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table): void {
            $table->dropForeign(['pinned_by']);
            $table->dropColumn(['is_pinned', 'pinned_at', 'pinned_by']);
        });
    }
};
