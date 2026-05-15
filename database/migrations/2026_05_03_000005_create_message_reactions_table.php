<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_reactions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('message_id');
            $table->uuid('user_id');
            $table->string('reaction', 20);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['message_id', 'user_id', 'reaction']);
            $table->index('message_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_reactions');
    }
};
