<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hangout_posts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('content', 280);
            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('joined_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->index('expires_at');
            $table->index('is_active');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hangout_posts');
    }
};
