<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->string('category', 50);
            $table->boolean('is_curated')->default(true);
            $table->boolean('is_approved')->default(true);
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamps();

            $table->index('category');
            $table->index(['is_approved', 'is_curated']);
            $table->index('usage_count');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
