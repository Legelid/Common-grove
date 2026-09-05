<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tag_subcategory_placements', function (Blueprint $table): void {
            $table->id();
            $table->uuid('tag_id');
            $table->foreignId('subcategory_id')->constrained('subcategories')->cascadeOnDelete();
            $table->boolean('is_primary')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->foreign('tag_id')
                ->references('id')
                ->on('tags')
                ->cascadeOnDelete();

            // A tag can't be placed in the same subcategory twice.
            $table->unique(['tag_id', 'subcategory_id']);
            $table->index('subcategory_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_subcategory_placements');
    }
};
