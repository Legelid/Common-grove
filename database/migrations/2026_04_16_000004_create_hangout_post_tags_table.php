<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hangout_post_tags', function (Blueprint $table): void {
            $table->uuid('hangout_post_id');
            $table->uuid('tag_id');

            $table->primary(['hangout_post_id', 'tag_id']);

            $table->foreign('hangout_post_id')
                ->references('id')
                ->on('hangout_posts')
                ->onDelete('cascade');

            $table->foreign('tag_id')
                ->references('id')
                ->on('tags')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hangout_post_tags');
    }
};
