<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocks', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('blocker_id');
            $table->uuid('blocked_id');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('blocker_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('blocked_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['blocker_id', 'blocked_id']);
            $table->index('blocker_id');
            $table->index('blocked_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
