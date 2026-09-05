<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_matches', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('matched_user_id');
            $table->date('week_of');
            $table->boolean('was_contacted')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('matched_user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['user_id', 'week_of']);
            $table->index('matched_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_matches');
    }
};
