<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->enum('type', ['direct', 'room']);
            $table->string('name', 100)->nullable();
            $table->uuid('hangout_post_id')->nullable();
            $table->uuid('created_by');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('hangout_post_id')
                ->references('id')
                ->on('hangout_posts')
                ->onDelete('set null');

            $table->foreign('created_by')
                ->references('id')
                ->on('users');

            $table->index('type');
            $table->index('is_active');
            $table->index('hangout_post_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
