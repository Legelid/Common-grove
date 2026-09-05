<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crisis_detections', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('conversation_id')->nullable();
            $table->string('triggered_keyword', 100);
            $table->timestamp('detected_at')->useCurrent();
            $table->boolean('was_dismissed')->default(false);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('set null');

            $table->index('user_id');
            $table->index('detected_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crisis_detections');
    }
};
