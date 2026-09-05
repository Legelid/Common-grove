<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversation_tags', function (Blueprint $table): void {
            $table->uuid('conversation_id');
            $table->uuid('tag_id');
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['conversation_id', 'tag_id']);

            $table->foreign('conversation_id')
                ->references('id')
                ->on('conversations')
                ->onDelete('cascade');

            $table->foreign('tag_id')
                ->references('id')
                ->on('tags')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_tags');
    }
};
